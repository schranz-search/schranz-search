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
use Schranz\Search\SEAL\Adapter\IndexerInterface;
use Schranz\Search\SEAL\Marshaller\Marshaller;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Task\AsyncTask;
use Schranz\Search\SEAL\Task\TaskInterface;

final class AlgoliaIndexer implements IndexerInterface
{
    private readonly Marshaller $marshaller;

    public function __construct(
        private readonly SearchClient $client,
    ) {
        $this->marshaller = new Marshaller(
            geoPointFieldConfig: [
                'name' => '_geoloc',
                'latitude' => 'lat',
                'longitude' => 'lng',
            ],
        );
    }

    public function save(Index $index, array $document, array $options = []): TaskInterface|null
    {
        $identifierField = $index->getIdentifierField();

        $batchIndexingResponse = $this->client->saveObject(
            $index->name,
            $this->marshaller->marshall($index->fields, $document),
            ['objectIDKey' => $identifierField->name],
        );

        if (!($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new AsyncTask(function () use ($batchIndexingResponse, $index) {
            $this->client->waitForTask(
                $index->name,
                $batchIndexingResponse['taskID'],
            );
        });
    }

    public function delete(Index $index, string $identifier, array $options = []): TaskInterface|null
    {
        $batchIndexingResponse = $this->client->deleteObject($index->name, $identifier);

        if (!($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new AsyncTask(function () use ($batchIndexingResponse, $index) {
            $this->client->waitForTask(
                $index->name,
                $batchIndexingResponse['taskID'],
            );
        });
    }
}
