<?php

namespace Schranz\Search\SEAL\Adapter\Meilisearch;

use Meilisearch\Client;
use Meilisearch\Exceptions\ApiException;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Schema\Field;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Task\AsyncTask;
use Schranz\Search\SEAL\Task\SyncTask;
use Schranz\Search\SEAL\Task\TaskInterface;

final class MeilisearchSchemaManager implements SchemaManagerInterface
{
    public function __construct(
        private readonly Client $client,
    ) {
    }

    public function existIndex(Index $index): bool
    {
        try {
            $this->client->getRawIndex($index->name);
        } catch (ApiException $e) {
            if ($e->httpStatus !== 404) {
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
        $this->client->createIndex(
            $index->name,
            [
                'primaryKey' => $index->getIdentifierField()->name,
            ]
        );

        $attributes = [
            'searchableAttributes' => $index->searchableFields,
            'filterableAttributes' => $index->filterableFields,
        ];

        $updateIndexResponse = $this->client->index($index->name)
            ->updateSettings($attributes);

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new AsyncTask(function() use ($updateIndexResponse) {
            $this->client->waitForTask($updateIndexResponse['taskUid']);
        });
    }
}
