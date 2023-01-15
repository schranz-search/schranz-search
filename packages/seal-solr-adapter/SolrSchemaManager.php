<?php

namespace Schranz\Search\SEAL\Adapter\Solr;

use Schranz\Search\SEAL\Task\SyncTask;
use Solarium\Client;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Task\AsyncTask;
use Schranz\Search\SEAL\Task\TaskInterface;
use Solarium\Exception\HttpException;

final class SolrSchemaManager implements SchemaManagerInterface
{
    public function __construct(
        private readonly Client $client,
    ) {
    }

    public function existIndex(Index $index): bool
    {
        $ping = $this->client->createPing([
            'collection' => $index->name,
            'path' => '/',
            'core' => 'index',
        ]);

        try {
            $result = $this->client->ping($ping);
        } catch (HttpException $e) {
            if ($e->getCode() !== 404) {
                throw $e;
            }

            return false;
        }

        return true;
    }

    public function dropIndex(Index $index, array $options = []): ?TaskInterface
    {
        $deleteIndexResponse = $this->client->deleteIndex($index->name);

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new AsyncTask(function() use ($deleteIndexResponse) {
            $this->client->waitForTask($deleteIndexResponse['taskUid']);
        });
    }

    public function createIndex(Index $index, array $options = []): ?TaskInterface
    {
        $query = $this->client->createCoreAdmin();

        $createAction = $query->createCreate();
        $createAction->setCore($index->name);
        $query->setAction($createAction);

        // TODO
        $attributes = [
            'searchableAttributes' => $index->searchableFields,
            'filterableAttributes' => $index->filterableFields,
            'sortableAttributes' => $index->sortableFields,
        ];

        $response = $this->client->coreAdmin($query);
        $result = $response->getStatusResult();

        var_dump($result);
        exit;

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
    }
}
