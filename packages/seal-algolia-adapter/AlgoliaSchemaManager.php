<?php

namespace Schranz\Search\SEAL\Adapter\Algolia;

use Algolia\AlgoliaSearch\SearchClient;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
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

        $indexResponse = $searchIndex->setSettings([
            'searchableAttributes' => [],
            'attributesForFaceting' => [
                $index->getIdentifierField()->name,
            ],
        ]);

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new AsyncTask(function() use ($indexResponse) {
            $indexResponse->wait();
        });
    }
}
