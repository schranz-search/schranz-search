<?php

declare(strict_types=1);

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

        $indexResponses = [];
        $indexResponse = $searchIndex->delete();
        $indexResponses[] = $indexResponse;

        if ([] !== $index->sortableFields) {
            // we need to wait for removing of primary index
            // see also: https://www.algolia.com/doc/guides/sending-and-managing-data/manage-indices-and-apps/manage-indices/how-to/delete-indices/#delete-multiple-indices
            $indexResponse->wait();

            // wait a little until replicas are converted to primary indices
            // see also: https://support.algolia.com/hc/en-us/requests/540200
            \usleep(10_000_000);
        }

        foreach ($index->sortableFields as $field) {
            foreach (['asc', 'desc'] as $direction) {
                $searchIndex = $this->client->initIndex(
                    $index->name . '__' . \str_replace('.', '_', $field) . '_' . $direction,
                );

                $indexResponses[] = $searchIndex->delete();
            }
        }

        if (!($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new AsyncTask(function () use ($indexResponses) {
            foreach ($indexResponses as $indexResponse) {
                $indexResponse->wait();
            }
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

        $indexResponses = [];
        $indexResponses[] = $searchIndex->setSettings($attributes);

        foreach ($index->sortableFields as $field) {
            foreach (['asc', 'desc'] as $direction) {
                $searchIndex = $this->client->initIndex(
                    $index->name . '__' . \str_replace('.', '_', $field) . '_' . $direction,
                );

                $indexResponses[] = $searchIndex->setSettings([
                    'ranking' => [
                        $direction . '(' . $field . ')',
                    ],
                ]);
            }
        }

        if (!($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new AsyncTask(function () use ($indexResponses) {
            foreach ($indexResponses as $indexResponse) {
                $indexResponse->wait();
            }
        });
    }
}
