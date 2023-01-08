<?php

namespace Schranz\Search\SEAL\Adapter\Algolia;

use Algolia\AlgoliaSearch\SearchClient;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Schema\Field;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Task\AsyncTask;
use Schranz\Search\SEAL\Task\TaskInterface;

final class AlgoliaSchemaManager implements SchemaManagerInterface
{
    public function __construct(
        private readonly SearchClient $client,
    ) {
    }

    public function existIndex(Index $index): bool
    {
        $index = $this->client->initIndex($index->name);

        return $index->exists();
    }

    public function dropIndex(Index $index, array $options = []): ?TaskInterface
    {
        $index = $this->client->initIndex($index->name);

        $indexResponse = $index->delete();

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new AsyncTask(function() use ($indexResponse) {
            $indexResponse->wait();
        });
    }

    public function createIndex(Index $index, array $options = []): ?TaskInterface
    {
        $searchIndex = $this->client->initIndex($index->name);

        $attributes = $this->getAttributes($index->fields);
        $attributes['attributesForFaceting'] = [
            $index->getIdentifierField()->name
        ];

        $indexResponse = $searchIndex->setSettings($attributes);

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new AsyncTask(function() use ($indexResponse) {
            $indexResponse->wait();
        });
    }

    /**
     * @param Field\AbstractField[] $fields
     *
     * @return array{
     *     searchableAttributes: array<string>,
     * }
     */
    private function getAttributes(array $fields): array
    {
        $attributes = [
            'searchableAttributes' => [],
        ];

        foreach ($fields as $name => $field) {
            if ($field instanceof Field\ObjectField) {
                foreach ($this->getAttributes($field->fields) as $attributeType => $fieldNames) {
                    foreach ($fieldNames as $fieldName) {
                        $attributes[$attributeType][] = $name . '.' . $fieldName;
                    }
                }

                continue;
            } elseif ($field instanceof Field\TypedField) {
                foreach ($field->types as $type => $fields) {
                    foreach ($this->getAttributes($fields) as $attributeType => $fieldNames) {
                        foreach ($fieldNames as $fieldName) {
                            $attributes[$attributeType][] = $name . '.' . $type . '.' . $fieldName;
                        }
                    }
                }

                continue;
            }

            if ($field->searchable) {
                $attributes['searchableAttributes'][] = $name;
            }
        }

        return $attributes;
    }
}
