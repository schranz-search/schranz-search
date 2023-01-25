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
        $searchIndex = $this->client->initIndex($index->name);

        $indexResponse = $searchIndex->delete();

        if (\count($index->sortableFields) > 0) {
            // we need to wait for removing of primary index
            // see also: https://www.algolia.com/doc/guides/sending-and-managing-data/manage-indices-and-apps/manage-indices/how-to/delete-indices/#delete-multiple-indices
            $indexResponse->wait();
        }

        foreach ($index->sortableFields as $field) {
            foreach (['asc', 'desc'] as $direction) {
                $searchIndex = $this->client->initIndex(
                    $index->name . '__' . \str_replace('.', '_', $field) . '_' . $direction
                );

                $indexResponse = $searchIndex->delete();
            }
        }

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

        $replicas = [];
        foreach ($index->sortableFields as $field) {
            foreach (['asc', 'desc'] as $direction) {
                $replicas[] = $index->name . '__' . \str_replace('.', '_', $field) . '_' . $direction;
            }
        }

        $attributes = [
            'searchableAttributes' => $index->searchableFields,
            'attributesForFaceting' => $index->filterableFields,
            'replicas' => $replicas,
        ];

        $indexResponse = $searchIndex->setSettings($attributes);

        foreach ($index->sortableFields as $field) {
            foreach (['asc', 'desc'] as $direction) {
                $searchIndex = $this->client->initIndex(
                    $index->name . '__' . \str_replace('.', '_', $field) . '_' . $direction
                );

                $searchIndex->setSettings([
                    'ranking' => [
                        $direction . '(' . $field . ')',
                    ],
                ]);
            }
        }

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new AsyncTask(function() use ($indexResponse) {
            $indexResponse->wait();
        });
    }
}
