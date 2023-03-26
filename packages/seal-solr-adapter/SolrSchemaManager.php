<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\Solr;

use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Schema\Field;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Task\SyncTask;
use Schranz\Search\SEAL\Task\TaskInterface;
use Solarium\Client;
use Solarium\Core\Client\Request;
use Solarium\QueryType\Server\Collections\Result\ClusterStatusResult;

final class SolrSchemaManager implements SchemaManagerInterface
{
    public function __construct(
        private readonly Client $client,
    ) {
    }

    public function existIndex(Index $index): bool
    {
        $collectionQuery = $this->client->createCollections();

        $action = $collectionQuery->createClusterStatus(['name' => $index->name]);
        $collectionQuery->setAction($action);

        /** @var ClusterStatusResult $result */
        $result = $this->client->collections($collectionQuery);

        return $result->getClusterState()->collectionExists($index->name);
    }

    public function dropIndex(Index $index, array $options = []): ?TaskInterface
    {
        $collectionQuery = $this->client->createCollections();

        $action = $collectionQuery->createDelete(['name' => $index->name]);
        $collectionQuery->setAction($action);

        $this->client->collections($collectionQuery);

        $configsetQuery = $this->client->createConfigsets();

        $action = $configsetQuery->createDelete()
            ->setName($index->name);
        $configsetQuery->setAction($action);
        $this->client->configsets($configsetQuery);

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
    }

    public function createIndex(Index $index, array $options = []): ?TaskInterface
    {
        $configsetQuery = $this->client->createConfigsets();

        $action = $configsetQuery->createCreate()
            ->setName($index->name)
            ->setBaseConfigSet('_default');
        $configsetQuery->setAction($action);

        $this->client->configsets($configsetQuery);

        $collectionQuery = $this->client->createCollections();

        $action = $collectionQuery->createCreate([
            'name' => $index->name,
            'numShards' => 1,
            'collection.configName' => $index->name,
        ]);
        $collectionQuery->setAction($action);

        $this->client->collections($collectionQuery);

        $indexFields = $this->createIndexFields($index->fields);

        foreach ($indexFields as $indexField) {
            $query = $this->client->createApi([
                'version' => Request::API_V1,
                'handler' => $index->name . '/schema',
                'method' => Request::METHOD_POST,
                'rawdata' => \json_encode([
                    'add-field' => $indexField,
                ], \JSON_THROW_ON_ERROR),
            ]);

            $this->client->execute($query);
        }

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
    }

    /**
     * @param Field\AbstractField[] $fields
     *
     * @return array<string, array{
     *     name: string,
     *     type: string,
     *     indexed: boolean,
     *     docValues: boolean,
     *     stored: boolean,
     *     useDocValuesAsStored?: boolean,
     *     multiValued: boolean,
     * }>
     */
    private function createIndexFields(array $fields, string $prefix = '', bool $isParentMultiple = false): array
    {
        /**
         * @var array<string, array{
         *     name: string,
         *     type: string,
         *     indexed: boolean,
         *     docValues: boolean,
         *     stored: boolean,
         *     useDocValuesAsStored: boolean,
         *     multiValued: boolean,
         * }> $indexFields
         */
        $indexFields = [];

        foreach ($fields as $name => $field) {
            $name = $prefix . $name;
            $isMultiple = $isParentMultiple || $field->multiple;

            match (true) {
                $field instanceof Field\IdentifierField => null, // TODO define primary field
                $field instanceof Field\TextField => $indexFields[$name] = [
                    'name' => $name,
                    'type' => $field->searchable ? 'text_general' : 'string',
                    'indexed' => $field->searchable,
                    'docValues' => $field->filterable || $field->sortable,
                    'stored' => false,
                    'useDocValuesAsStored' => false,
                    'multiValued' => $isMultiple,
                ],
                $field instanceof Field\BooleanField => $indexFields[$name] = [
                    'name' => $name,
                    'type' => 'bool',
                    'indexed' => $field->searchable,
                    'docValues' => $field->filterable || $field->sortable,
                    'stored' => false,
                    'useDocValuesAsStored' => false,
                    'multiValued' => $isMultiple,
                ],
                $field instanceof Field\DateTimeField => $indexFields[$name] = [
                    'name' => $name,
                    'type' => 'pdate',
                    'indexed' => $field->searchable,
                    'docValues' => $field->filterable || $field->sortable,
                    'stored' => false,
                    'useDocValuesAsStored' => false,
                    'multiValued' => $isMultiple,
                ],
                $field instanceof Field\IntegerField => $indexFields[$name] = [
                    'name' => $name,
                    'type' => 'pint',
                    'indexed' => $field->searchable,
                    'docValues' => $field->filterable || $field->sortable,
                    'stored' => false,
                    'useDocValuesAsStored' => false,
                    'multiValued' => $isMultiple,
                ],
                $field instanceof Field\FloatField => $indexFields[$name] = [
                    'name' => $name,
                    'type' => 'pfloat',
                    'indexed' => $field->searchable,
                    'docValues' => $field->filterable || $field->sortable,
                    'stored' => false,
                    'useDocValuesAsStored' => false,
                    'multiValued' => $isMultiple,
                ],
                $field instanceof Field\ObjectField => $indexFields = \array_replace($indexFields, $this->createIndexFields($field->fields, $name . '.', $isMultiple)),
                $field instanceof Field\TypedField => \array_map(function ($fields, $type) use ($name, &$indexFields, $isMultiple) {
                    $indexFields = \array_replace($indexFields, $this->createIndexFields($fields, $name . '.' . $type . '.', $isMultiple));
                }, $field->types, \array_keys($field->types)),
                default => throw new \RuntimeException(\sprintf('Field type "%s" is not supported.', $field::class)),
            };

            if ($field instanceof Field\TextField && $field->searchable && ($field->filterable || $field->sortable)) {
                // add additional raw field for field which is filterable/sortable but also searchable
                $fieldSettings = $indexFields[$name];

                $fieldSettings['name'] = $name . '.raw';
                $fieldSettings['type'] = 'string';
                $indexFields[$name . '.raw'] = $fieldSettings;

                $indexFields[$name]['docValues'] = false;
            }
        }

        if ('' === $prefix) {
            $indexFields['_source'] = [
                'name' => '_source',
                'type' => 'string',
                'indexed' => false,
                'docValues' => false,
                'stored' => true,
                'useDocValuesAsStored' => false,
                'multiValued' => false,
            ];
        }

        return $indexFields;
    }
}
