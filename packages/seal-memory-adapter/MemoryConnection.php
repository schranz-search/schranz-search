<?php

namespace Schranz\Search\SEAL\Adapter\Memory;

use Schranz\Search\SEAL\Adapter\ConnectionInterface;
use Schranz\Search\SEAL\Marshaller\Marshaller;
use Schranz\Search\SEAL\Schema\Field;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Search\Condition;
use Schranz\Search\SEAL\Search\Result;
use Schranz\Search\SEAL\Search\Search;
use Schranz\Search\SEAL\Task\SyncTask;
use Schranz\Search\SEAL\Task\TaskInterface;

final class MemoryConnection implements ConnectionInterface
{
    private Marshaller $marshaller;

    public function __construct()
    {
        $this->marshaller = new Marshaller();
    }

    public function save(Index $index, array $document, array $options = []): ?TaskInterface
    {
        $document = MemoryStorage::save($index, $this->marshaller->marshall($index->fields, $document));

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask($document);
    }

    public function delete(Index $index, string $identifier, array $options = []): ?TaskInterface
    {
        MemoryStorage::delete($index, $identifier);

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
    }

    public function search(Search $search): Result
    {
        $documents = [];

        /** @var Index $index */
        foreach ($search->indexes as $index) {
            foreach (MemoryStorage::getDocuments($index) as $identifier => $document) {
                $identifier = (string) $identifier;

                if (count($search->filters) === 0) {
                    $documents[] = $document;

                    continue;
                }

                foreach ($search->filters as $filter) {
                    if ($filter instanceof Condition\IdentifierCondition) {
                        if ($filter->identifier !== $identifier) {
                            continue 2;
                        }
                    } elseif ($filter instanceof Condition\SearchCondition) {
                        $searchableDocument = $this->getSearchableDocument($index->fields, $document);

                        $text = \json_encode($searchableDocument, JSON_THROW_ON_ERROR);
                        $terms = \explode(' ', $filter->query);

                        foreach ($terms as $term) {
                            if (!\str_contains($text, $term)) {
                                continue 3;
                            }
                        }
                    } elseif ($filter instanceof Condition\EqualCondition) {
                        if (\str_contains($filter->field, '.')) {
                            throw new \RuntimeException('Nested fields are not supported yet.');
                        }

                        $values = (array) ($document[$filter->field] ?? []);

                        if (!\in_array($filter->value, $values, true)) {
                            continue 2;
                        }
                    } elseif ($filter instanceof Condition\NotEqualCondition) {
                        if (\str_contains($filter->field, '.')) {
                            throw new \RuntimeException('Nested fields are not supported yet.');
                        }

                        $values = (array) ($document[$filter->field] ?? []);

                        if (\in_array($filter->value, $values, true)) {
                            continue 2;
                        }
                    } else {
                        throw new \LogicException($filter::class . ' filter not implemented.');
                    }
                }

                $documents[] = $this->marshaller->unmarshall($index->fields, $document);
            }
        }

        $documents = \array_slice($documents, $search->offset, $search->limit);

        $generator = (function() use ($documents): \Generator {
            foreach ($documents as $document) {
                yield $document;
            }
        });

        return new Result(
            $generator(),
            count($documents),
        );
    }

    /**
     * @param Field\AbstractField[] $fields
     * @param array<string, mixed> $document
     *
     * @return array<string, mixed>
     */
    private function getSearchableDocument(array $fields, array $document): array
    {
        foreach ($fields as $field) {
            if (!isset($document[$field->name])) {
                continue;
            }

            if (!$field->searchable) {
                unset($document[$field->name]);

                continue;
            }

            match(true) {
                $field instanceof Field\ObjectField => $document[$field->name] = $this->getSearchableObjectFields($field, $document[$field->name]),
                $field instanceof Field\TypedField => $document[$field->name] = $this->getSearchableTypedFields($field, $document[$field->name]),
                default => null,
            };
        }

        return $document;
    }

    /**
     * @param Field\AbstractField[] $fields
     * @param array<string, mixed> $document
     *
     * @return array<string, mixed>
     */
    private function getSearchableObjectFields(Field\ObjectField $field, array $data)
    {
        if (!$field->multiple) {
            return $this->getSearchableDocument($field->fields, $data);
        }

        $documents = [];
        foreach ($data as $sub) {
            $documents[] = $this->getSearchableDocument($field->fields, $sub);
        }

        return $documents;
    }

    /**
     * @param Field\AbstractField[] $fields
     * @param array<string, mixed> $document
     *
     * @return array<string, mixed>
     */
    private function getSearchableTypedFields(Field\TypedField $field, array $data)
    {
        $documents = [];
        foreach ($data as $type => $sub) {
            if (!$field->multiple) {
                $sub = [$sub];
            }

            $typeFields = $field->types[$type];
            foreach ($sub as $item) {
                $subDocument = $this->getSearchableDocument($typeFields, $item);

                if (!$field->multiple) {
                    return [$type => $subDocument];
                }

                $documents[$type][] = $subDocument;
            }
        }

        return $documents;
    }
}
