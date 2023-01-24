<?php

namespace Schranz\Search\SEAL\Adapter\Solr;

use Schranz\Search\SEAL\Task\SyncTask;
use Solarium\Client;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Schema\Field;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Task\TaskInterface;
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
                'handler' => $index->name.'/schema',
                'method' => Request::METHOD_POST,
                'rawdata' => json_encode([
                    'add-field' => $indexField,
                ], \JSON_THROW_ON_ERROR),
            ]);

            $this->client->execute($query);
        }

        // TODO create schema fields
        /*
        $attributes = [
            'searchableAttributes' => $index->searchableFields,
            'filterableAttributes' => $index->filterableFields,
            'sortableAttributes' => $index->sortableFields,
        ];
         */

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
    }

    /**
     * @param Field\AbstractField[] $fields
     *
     * @return array<string, mixed>
     */
    private function createIndexFields(array $fields): array
    {
        $indexFields = [];

        foreach ($fields as $name => $field) {
            match (true) {
                $field instanceof Field\IdentifierField => null, // TODO define primary field
                $field instanceof Field\TextField => $indexFields[$name] = [
                    'name' => $name,
                    'type' => 'string',
                    'indexed' => $field->searchable,
                    'docValues' => $field->filterable || $field->sortable,
                    'stored' => false,
                    'multiValued' => $field->multiple,
                ],
                $field instanceof Field\BooleanField => $indexFields[$name] = [
                    'name' => $name,
                    'type' => 'bool',
                    'indexed' => $field->searchable,
                    'docValues' => $field->filterable || $field->sortable,
                    'stored' => false,
                    'multiValued' => $field->multiple,
                ],
                $field instanceof Field\DateTimeField => $indexFields[$name] = [
                    'name' => $name,
                    'type' => 'pdate',
                    'indexed' => $field->searchable,
                    'docValues' => $field->filterable || $field->sortable,
                    'stored' => false,
                    'multiValued' => $field->multiple,
                ],
                $field instanceof Field\IntegerField => $indexFields[$name] = [
                    'name' => $name,
                    'type' => 'pint',
                    'indexed' => $field->searchable,
                    'docValues' => $field->filterable || $field->sortable,
                    'stored' => false,
                    'multiValued' => $field->multiple,
                ],
                $field instanceof Field\FloatField => $indexFields[$name] = [
                    'name' => $name,
                    'type' => 'pfloat',
                    'indexed' => $field->searchable,
                    'docValues' => $field->filterable || $field->sortable,
                    'stored' => false,
                    'multiValued' => $field->multiple,
                ],
                default => null,
                /* TODO implement ObjectField and TypedField
                $field instanceof Field\ObjectField => $fields[$name] = [
                    'type' => 'object',
                    'properties' => $this->createPropertiesMapping($field->fields),
                ],
                $field instanceof Field\TypedField => $fields = \array_replace($properties, $this->createTypedFieldMapping($name, $field)),
                default => throw new \RuntimeException(sprintf('Field type "%s" is not supported.', get_class($field))),
                */
            };
        }

        return $indexFields;
    }
}
