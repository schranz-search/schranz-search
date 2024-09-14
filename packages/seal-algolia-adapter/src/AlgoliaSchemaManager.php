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

namespace Schranz\Search\SEAL\Adapter\Algolia;

use Algolia\AlgoliaSearch\Api\SearchClient;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Schema\Field\GeoPointField;
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
        return $this->client->indexExists($index->name);
    }

    public function dropIndex(Index $index, array $options = []): TaskInterface|null
    {
        $indexResponses = [];
        $indexResponses[] = [
            'indexName' => $index->name,
            ...$this->client->deleteIndex( // @phpstan-ignore-line
                $index->name,
            ),
        ];

        if ([] !== $index->sortableFields) {
            // we need to wait for removing of primary index
            // see also: https://www.algolia.com/doc/guides/sending-and-managing-data/manage-indices-and-apps/manage-indices/how-to/delete-indices/#delete-multiple-indices
            // see also: https://support.algolia.com/hc/en-us/requests/540200
            $this->client->waitForTask(
                $indexResponses[0]['indexName'],
                $indexResponses[0]['taskID'],
            );
        }

        foreach ($index->sortableFields as $field) {
            foreach (['asc', 'desc'] as $direction) {
                $sortIndexName = $index->name . '__' . \str_replace('.', '_', $field) . '_' . $direction;
                $indexResponses[] = [
                    'indexName' => $sortIndexName,
                    ...$this->client->deleteIndex( // @phpstan-ignore-line
                        $sortIndexName,
                    ),
                ];
            }
        }

        if (!($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new AsyncTask(function () use ($indexResponses) {
            foreach ($indexResponses as $indexResponse) {
                $this->client->waitForTask(
                    $indexResponse['indexName'],
                    $indexResponse['taskID'],
                );
            }
        });
    }

    public function createIndex(Index $index, array $options = []): TaskInterface|null
    {
        $geoPointField = $index->getGeoPointField();
        $replicas = [];
        foreach ($index->sortableFields as $field) {
            if ($geoPointField?->name === $field) {
                $field = '_geoloc';
            }

            foreach (['asc', 'desc'] as $direction) {
                $replicas[] = $index->name . '__' . \str_replace('.', '_', $field) . '_' . $direction;
            }
        }

        $attributes = [
            'searchableAttributes' => $index->searchableFields,
            'attributesForFaceting' => $index->filterableFields,
            'replicas' => $replicas,
        ];

        if ($geoPointField instanceof GeoPointField) {
            foreach ($attributes as $listKey => $list) {
                foreach ($list as $key => $value) {
                    if ($value === $geoPointField->name) {
                        $attributes[$listKey][$key] = '_geoloc';
                    }
                }
            }
        }

        $indexResponses = [];
        $indexResponses[] = [
            'indexName' => $index->name,
            ...$this->client->setSettings($index->name, $attributes), // @phpstan-ignore-line
        ];

        foreach ($index->sortableFields as $field) {
            foreach (['asc', 'desc'] as $direction) {
                $sortIndexName = $index->name . '__' . \str_replace('.', '_', $field) . '_' . $direction;

                $indexResponses[] = [
                    'indexName' => $sortIndexName,
                    ...$this->client->setSettings(  // @phpstan-ignore-line
                        $sortIndexName,
                        [
                            'ranking' => [
                                $direction . '(' . $field . ')',
                            ],
                        ],
                    ),
                ];
            }
        }

        if (!($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new AsyncTask(function () use ($indexResponses) {
            foreach ($indexResponses as $indexResponse) {
                $this->client->waitForTask(
                    $indexResponse['indexName'],
                    $indexResponse['taskID'],
                );
            }
        });
    }
}
