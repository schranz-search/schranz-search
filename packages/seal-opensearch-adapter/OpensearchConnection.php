<?php

namespace Schranz\Search\SEAL\Adapter\Opensearch;

use OpenSearch\Client;
use OpenSearch\Common\Exceptions\Missing404Exception;
use Schranz\Search\SEAL\Adapter\ConnectionInterface;
use Schranz\Search\SEAL\Marshaller\Marshaller;
use Schranz\Search\SEAL\Schema\Exception\FieldByPathNotFoundException;
use Schranz\Search\SEAL\Schema\Field;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Search\Condition;
use Schranz\Search\SEAL\Search\Result;
use Schranz\Search\SEAL\Search\Search;
use Schranz\Search\SEAL\Task\SyncTask;
use Schranz\Search\SEAL\Task\TaskInterface;

final class OpensearchConnection implements ConnectionInterface
{
    private Marshaller $marshaller;

    public function __construct(
        private readonly Client $client,
    ) {
        $this->marshaller = new Marshaller();
    }

    public function save(Index $index, array $document, array $options = []): ?TaskInterface
    {
        $identifierField = $index->getIdentifierField();

        /** @var string|null $identifier */
        $identifier = ((string) $document[$identifierField->name]) ?? null;

        $document = $this->marshaller->marshall($index->fields, $document);

        $data = $this->client->index([
            'index' => $index->name,
            'id' => $identifier,
            'body' => $document,
            // TODO refresh should be refactored with async tasks
            'refresh' => $options['return_slow_promise_result'] ?? false, // update document immediately, so it is available in the `/_search` api directly
        ]);

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        $document[$identifierField->name] = $data['_id'];

        return new SyncTask($document);
    }

    public function delete(Index $index, string $identifier, array $options = []): ?TaskInterface
    {
        $data = $this->client->delete([
            'index' => $index->name,
            'id' => $identifier,
            // TODO refresh should be refactored with async tasks
            'refresh' => $options['return_slow_promise_result'] ?? false, // update document immediately, so it is no longer available in the `/_search` api directly
        ]);

        if ($data['result'] !== 'deleted') {
            throw new \RuntimeException('Unexpected error while delete document with identifier "' . $identifier . '" from Index "' . $index->name . '".');
        }

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
    }

    public function search(Search $search): Result
    {
        // optimized single document query
        if (
            count($search->indexes) === 1
            && count($search->filters) === 1
            && $search->filters[0] instanceof Condition\IdentifierCondition
            && $search->offset === 0
            && $search->limit === 1
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
            match (true) {
                $filter instanceof Condition\IdentifierCondition => $query['ids']['values'][] = $filter->identifier,
                $filter instanceof Condition\SearchCondition => $query['query_string']['query'] = $filter->query,
                $filter instanceof Condition\EqualCondition => $query['bool']['must'][]['term'][$this->getFilterField($search->indexes, $filter->field)]['value'] = $filter->value,
                $filter instanceof Condition\NotEqualCondition => $query['bool']['must_not'][]['term'][$this->getFilterField($search->indexes, $filter->field)]['value'] = $filter->value,
                $filter instanceof Condition\GreaterThanCondition => $query['bool']['must'][]['range'][$this->getFilterField($search->indexes, $filter->field)]['gt'] = $filter->value,
                $filter instanceof Condition\GreaterThanEqualCondition => $query['bool']['must'][]['range'][$this->getFilterField($search->indexes, $filter->field)]['gte'] = $filter->value,
                $filter instanceof Condition\LessThanCondition => $query['bool']['must'][]['range'][$this->getFilterField($search->indexes, $filter->field)]['lt'] = $filter->value,
                $filter instanceof Condition\LessThanEqualCondition => $query['bool']['must'][]['range'][$this->getFilterField($search->indexes, $filter->field)]['lte'] = $filter->value,
                default => throw new \LogicException($filter::class . ' filter not implemented.'),
            };
        }

        if (count($query) === 0) {
            $query['match_all'] = new \stdClass();
        }

        $sort = [];
        foreach ($search->sortBys as $field => $direction) {
            $sort[] = [$field => $direction];
        }

        $body = [
            'sort' => $sort,
            'query' => $query,
        ];

        if ($search->offset) {
            $body['from'] = $search->offset;
        }

        if ($search->limit) {
            $body['size'] = $search->limit;
        }

        $searchResult = $this->client->search([
            'index' => implode(',', $indexesNames),
            'body' => $body,
        ]);

        return new Result(
            $this->hitsToDocuments($search->indexes, $searchResult['hits']['hits']),
            $searchResult['hits']['total']['value'],
        );
    }

    /**
     * @param Index[] $indexes
     * @param array<string, mixed> $searchResult
     *
     * @return \Generator<array<string, mixed>>
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

            yield $this->marshaller->unmarshall($index->fields, $hit['_source']);
        }
    }

    private function getFilterField(array $indexes, string $name): string
    {
        foreach ($indexes as $index) {
            try {
                $field = $index->getFieldByPath($name);

                if ($field instanceof Field\TextField) {
                    return $name . '.raw';
                }

                return $name;
            } catch (FieldByPathNotFoundException $e) {
                // ignore when field is not found and use go to next index instead
            }
        }

        return $name;
    }
}
