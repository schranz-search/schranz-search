<?php

namespace Schranz\Search\SEAL\Adapter\Opensearch;

use OpenSearch\Client;
use OpenSearch\Common\Exceptions\Missing404Exception;
use Schranz\Search\SEAL\Adapter\ConnectionInterface;
use Schranz\Search\SEAL\Schema\Field\AbstractField;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Search\Condition\IdentifierCondition;
use Schranz\Search\SEAL\Schema\Field;
use Schranz\Search\SEAL\Search\Result;
use Schranz\Search\SEAL\Search\Search;

final class OpensearchConnection implements ConnectionInterface
{
    public function __construct(
        private readonly Client $client,
    ) {
    }

    public function save(Index $index, array $document): array
    {
        $identifierField = $index->getIdentifierField();

        /** @var string|null $identifier */
        $identifier = ((string) $document[$identifierField->name]) ?? null;

        $document = $this->normalizeDocument($index->fields, $document);

        $data = $this->client->index([
            'index' => $index->name,
            'id' => $identifier,
            'body' => $document,
            'refresh' => true, // index document immediately, so it is available in the `/_search` api directly
        ]);

        if ($data['result'] !== 'created') {
            throw new \RuntimeException('Unexpected error while save document with identifier "' . $identifier . '" into Index "' . $index->name . '".');
        }

        $document[$identifierField->name] = $data['_id'];

        return $document;
    }

    public function delete(Index $index, string $identifier): void
    {
        $data = $this->client->delete([
            'index' => $index->name,
            'id' => $identifier,
            'refresh' => true, // update document immediately, so it is no longer available in the `/_search` api directly
        ]);

        if ($data['result'] !== 'deleted') {
            throw new \RuntimeException('Unexpected error while delete document with identifier "' . $identifier . '" from Index "' . $index->name . '".');
        }
    }

    public function search(Search $search): Result
    {
        // optimized single document query
        if (
            count($search->indexes) === 1
            && count($search->filters) === 1
            && $search->filters[0] instanceof IdentifierCondition
            && ($search->offset === null || $search->offset === 0)
            && ($search->limit === null || $search->limit > 0)
        ) {
            try {
                $searchResult = $this->client->get([
                    'index' => $search->indexes[\array_key_first($search->indexes)]->name,
                    'id' => $search->filters[0]->identifier,
                ]);
            } catch (Missing404Exception $e) {
                return new Result(
                    $this->hitsToDocuments($search->indexes, []),
                    0
                );
            }

            return new Result(
                $this->hitsToDocuments($search->indexes, [$searchResult]),
                1
            );
        }

        $indexesNames = [];
        foreach ($search->indexes as $index) {
            $indexesNames[] = $index->name;
        }

        $query = [];
        foreach ($search->filters as $filter) {
            if ($filter instanceof IdentifierCondition) {
                $query['ids']['values'][] = $filter->identifier;
            } else {
                throw new \LogicException($filter::class . ' filter not implemented.');
            }
        }

        if (count($query) === 0) {
            $query['match_all'] = new \stdClass();
        }

        $searchResult = $this->client->search([
            'index' => count($indexesNames) === 1 ? $indexesNames[0] : $indexesNames,
            'body' => [
                'query' => $query,
            ],
        ]);

        return new Result(
            $this->hitsToDocuments($search->indexes, $searchResult['hits']['hits']),
            $searchResult['hits']['total']['value'],
        );
    }

    /**
     * @param AbstractField[] $fields
     * @param array<string, mixed> $document
     *
     * @return array<string, mixed>
     */
    private function normalizeDocument(array $fields, array $document): array
    {
        $normalizedDocument = [];

        foreach ($fields as $name => $field) {
            if (!\array_key_exists($field->name, $document)) {
                continue;
            }

            if ($field->multiple && !\is_array($document[$field->name])) {
                throw new \RuntimeException('Field "' . $field->name . '" is multiple but value is not an array.');
            }

            match (true) {
                $field instanceof Field\ObjectField => $normalizedDocument[$name] = $this->normalizeObjectFields($document[$field->name], $field),
                $field instanceof Field\TypedField => $normalizedDocument = \array_replace($normalizedDocument, $this->normalizeTypedFields($name, $document[$field->name], $field)),
                default => $normalizedDocument[$name] = $document[$field->name],
            };
        }

        return $normalizedDocument;
    }

    /**
     * @param array<string, mixed> $document
     *
     * @return array<string, mixed>
     */
    private function normalizeObjectFields(array $document, Field\ObjectField $field): array
    {
        if (!$field->multiple) {
            return $this->normalizeDocument($field->fields, $document);
        }

        $documents = [];
        foreach ($document as $data) {
            $documents[] = $this->normalizeDocument($field->fields, $data);
        }

        return $documents;
    }

    /**
     * @param array<string, mixed> $document
     *
     * @return array<string, mixed>
     */
    private function normalizeTypedFields(string $name, array $document, Field\TypedField $field): array
    {
        $normalizedFields = [];

        if (!$field->multiple) {
            $document = [$document];
        }

        foreach ($document as $originalIndex => $data) {
            /** @var string|null $type */
            $type = $data[$field->typeField] ?? null;
            if ($type === null || !\array_key_exists($type, $field->types)) {
                throw new \RuntimeException('Expected type field "' . $field->typeField . '" not found in document.');
            }

            $typedFields = $field->types[$type];

            $normalizedData = \array_replace([
                '_type' => $type,
                '_originalIndex' => $originalIndex,
            ], $this->normalizeDocument($typedFields, $data));

            if ($field->multiple) {
                $normalizedFields[$name . '.' . $type][] = $normalizedData;

                continue;
            }

            $normalizedFields[$name . '.' . $type] = $normalizedData;
        }

        return $normalizedFields;
    }

    /**
     * @param Index[] $indexes
     * @param array<string, mixed> $searchResult
     *
     * @return array<string, mixed>
     */
    private function hitsToDocuments(array $indexes, array $hits): \Generator
    {
        $indexesByInternalName = [];
        foreach ($indexes as $index) {
            $indexesByInternalName[$index->name] = $index;
        }

        foreach ($hits as $hit) {
            $index = $indexesByInternalName[$hit['_index']] ?? null;
            if ($index === null) {
                throw new \RuntimeException('SchemaMetadata for Index "' . $hit['_index'] . '" not found.');
            }

            $denormalizedDocument = $this->denormalizeDocument($index->fields, $hit['_source']);

            yield $denormalizedDocument;
        }
    }

    /**
     * @param AbstractField[] $fields
     * @param array<string, mixed> $normalizedDocument
     *
     * @return array<string, mixed>
     */
    private function denormalizeDocument(array $fields, array $normalizedDocument): array
    {
        $denormalizedDocument = [];

        foreach ($fields as $name => $field) {
            if (!\array_key_exists($name, $normalizedDocument) && !$field instanceof Field\TypedField ) {
                continue;
            }

            match (true) {
                $field instanceof Field\ObjectField => $denormalizedDocument[$field->name] = $this->denormalizeObjectFields($normalizedDocument[$name], $field),
                $field instanceof Field\TypedField => $denormalizedDocument = \array_replace($denormalizedDocument, $this->denormalizeTypedFields($name, $normalizedDocument, $field)),
                default => $denormalizedDocument[$field->name] = $normalizedDocument[$name] ?? ($field->multiple ? [] : null),
            };
        }

        return $denormalizedDocument;
    }

    /**
     * @param array<string, mixed> $document
     *
     * @return array<string, mixed>
     */
    private function denormalizeTypedFields(string $name, array $document, Field\TypedField $field): array
    {
        $denormalizedFields = [];

        foreach ($field->types as $type => $typedFields) {
            if (!isset($document[$name . '.' . $type])) {
                continue;
            }

            $dataList = $field->multiple ? $document[$name . '.' . $type] : [$document[$name . '.' . $type]];

            foreach ($dataList as $data) {
                $denormalizedData = \array_replace([$field->typeField => $type], $this->denormalizeDocument($typedFields, $data));

                if ($field->multiple) {
                    /** @var string|int|null $originalIndex */
                    $originalIndex = $data['_originalIndex'] ?? null;
                    if ($originalIndex === null) {
                        throw new \RuntimeException('Expected "_originalIndex" field not found in document.');
                    }

                    $denormalizedFields[$name][$originalIndex] = $denormalizedData;

                    continue;
                }

                $denormalizedFields[$name] = $denormalizedData;
            }
        }

        return $denormalizedFields;
    }

    /**
     * @param array<string, mixed> $document
     *
     * @return array<string, mixed>
     */
    private function denormalizeObjectFields(array $document, Field\ObjectField $field): array
    {
        if (!$field->multiple) {
            return $this->denormalizeDocument($field->fields, $document);
        }

        $documents = [];
        foreach ($document as $data) {
            $documents[] = $this->denormalizeDocument($field->fields, $data);
        }

        return $documents;
    }
}
