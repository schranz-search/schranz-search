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

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
    }

    public function createIndex(Index $index, array $options = []): ?TaskInterface
    {
        $collectionQuery = $this->client->createCollections();

        $action = $collectionQuery->createCreate([
            'name' => $index->name,
            'numShards' => 1,
        ]);
        $collectionQuery->setAction($action);

        $this->client->collections($collectionQuery);

        $requests = $this->createRequests($index->fields);

        foreach ($requests as $request) {
            $query = $this->client->createApi([
                'version' => Request::API_V1,
                'handler' => $index->name.'/schema',
                'method' => Request::METHOD_POST,
                'rawdata' => json_encode($request, \JSON_THROW_ON_ERROR),
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
    private function createRequests(array $fields): array
    {
        $requests = [];

        foreach ($fields as $name => $field) {
            match (true) {
                $field instanceof Field\IdentifierField => null,
                $field instanceof Field\TextField => $requests[$name] = [
                    'add-field' => [
                        'name' => $name,
                        'type' => 'string',
                        'indexed' => $field->searchable,
                        'docValues' => $field->filterable || $field->sortable,
                        'stored' => false,
                        'multiValued' => $field->multiple,
                    ],
                ],
                $field instanceof Field\BooleanField => $requests[$name] = [
                    'add-field' => [
                        'name' => $name,
                        'type' => 'bool',
                        'indexed' => $field->searchable,
                        'docValues' => $field->filterable || $field->sortable,
                        'stored' => false,
                        'multiValued' => $field->multiple,
                    ],
                ],
                $field instanceof Field\DateTimeField => $requests[$name] = [
                    'add-field' => [
                        'name' => $name,
                        'type' => 'pdate',
                        'indexed' => $field->searchable,
                        'docValues' => $field->filterable || $field->sortable,
                        'stored' => false,
                        'multiValued' => $field->multiple,
                    ],
                ],
                $field instanceof Field\IntegerField => $requests[$name] = [
                    'add-field' => [
                        'name' => $name,
                        'type' => 'pint',
                        'indexed' => $field->searchable,
                        'docValues' => $field->filterable || $field->sortable,
                        'stored' => false,
                        'multiValued' => $field->multiple,
                    ],
                ],
                $field instanceof Field\FloatField => $requests[$name] = [
                    'add-field' => [
                        'name' => $name,
                        'type' => 'pfloat',
                        'indexed' => $field->searchable,
                        'docValues' => $field->filterable || $field->sortable,
                        'stored' => false,
                        'multiValued' => $field->multiple,
                    ],
                ],
                default => null,
                /*
                $field instanceof Field\ObjectField => $fields[$name] = [
                    'type' => 'object',
                    'properties' => $this->createPropertiesMapping($field->fields),
                ],
                $field instanceof Field\TypedField => $fields = \array_replace($properties, $this->createTypedFieldMapping($name, $field)),
                default => throw new \RuntimeException(sprintf('Field type "%s" is not supported.', get_class($field))),
                */
            };
        }

        return $requests;
    }
}
