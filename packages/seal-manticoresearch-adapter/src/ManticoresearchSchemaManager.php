<?php

declare(strict_types=1);

/*
 * This file is part of the Schranz Search package.
 *
 * (c) Alexander Schranz <alexander@sulu.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Schranz\Search\SEAL\Adapter\Manticoresearch;

use Manticoresearch\Client;
use Manticoresearch\Exceptions\ResponseException;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Schema\Field;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Task\SyncTask;
use Schranz\Search\SEAL\Task\TaskInterface;

final class ManticoresearchSchemaManager implements SchemaManagerInterface
{
    public function __construct(
        private readonly Client $client,
    ) {
    }

    public function existIndex(Index $index): bool
    {
        $searchIndex = $this->client->index($index->name);

        try {
            $searchIndex->describe();
        } catch (ResponseException) {
            return false;
        }

        return true;
    }

    public function dropIndex(Index $index, array $options = []): ?TaskInterface
    {
        $searchIndex = $this->client->index($index->name);

        $searchIndex->drop();

        if (!($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
    }

    public function createIndex(Index $index, array $options = []): ?TaskInterface
    {
        $searchIndex = $this->client->index($index->name);

        $fields = $this->createIndexFields($index->fields);

        $searchIndex->create($fields);

        if (!($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
    }

    /**
     * @param Field\AbstractField[] $fields
     *
     * @return array<string, mixed>
     */
    private function createIndexFields(array $fields, string $prefix = '', bool $isParentMultiple = false): array
    {
        $indexFields = [];

        foreach ($fields as $name => $field) {
            $name = $prefix . $name;
            $isMultiple = $isParentMultiple || $field->multiple;
            /** @var bool $isSearchable */
            $isSearchable = $field->searchable;

            match (true) {
                $field instanceof Field\IdentifierField => null,
                $field instanceof Field\TextField => $indexFields[$name] = [
                    'type' => $isSearchable ? 'text' : 'string',
                    'options' => \array_merge(
                        $isSearchable ? ['indexed'] : [],
                        ($field->sortable || $field->filterable) ? ['attribute'] : [],
                    ),
                ],
                $field instanceof Field\BooleanField => $indexFields[$name] = [
                    'type' => 'bool',
                    'options' => \array_merge(
                        $isSearchable ? ['indexed'] : [],
                        ($field->sortable || $field->filterable) ? ['attribute'] : [],
                    ),
                ],
                $field instanceof Field\DateTimeField => $indexFields[$name] = [
                    'type' => 'timestamp',
                    'options' => \array_merge(
                        $isSearchable ? ['indexed'] : [],
                    ),
                ],
                $field instanceof Field\IntegerField => $indexFields[$name] = [
                    'type' => $isMultiple ? 'multi' : 'integer',
                    'options' => \array_merge(
                        $isSearchable ? ['indexed'] : [],
                    ),
                ],
                $field instanceof Field\FloatField => $indexFields[$name] = [
                    'type' => 'float',
                    'options' => \array_merge(
                        $isSearchable ? ['indexed'] : [],
                    ),
                ],
                $field instanceof Field\ObjectField => $indexFields = \array_replace($indexFields, $this->createIndexFields($field->fields, $name . '_', $isMultiple)),
                $field instanceof Field\TypedField => \array_map(function ($fields, $type) use ($name, &$indexFields, $isMultiple) {
                    $indexFields = \array_replace($indexFields, $this->createIndexFields($fields, $name . '_' . $type . '_', $isMultiple));
                }, $field->types, \array_keys($field->types)),
                default => throw new \RuntimeException(\sprintf('Field type "%s" is not supported.', $field::class)),
            };
        }

        if ('' === $prefix) {
            $indexFields['_source'] = [
                'type' => 'text',
                'options' => [],
            ];
        }

        return $indexFields;
    }
}
