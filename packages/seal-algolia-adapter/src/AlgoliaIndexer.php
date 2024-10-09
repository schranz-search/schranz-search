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
use Schranz\Search\SEAL\Adapter\BulkHelper;
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

        $document = $this->marshaller->marshall($index->fields, $document);
        $document['objectID'] = $document[$identifierField->name]; // TODO check objectIDKey instead see: https://github.com/algolia/algoliasearch-client-php/issues/738

        $batchIndexingResponse = $this->client->saveObject(
            $index->name,
            $document,
        );

        if (!($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new AsyncTask(function () use ($batchIndexingResponse, $index, $document) {
            \assert(isset($batchIndexingResponse['taskID']) && \is_int($batchIndexingResponse['taskID']), 'Task ID is expected to be returned by algolia client.');

            $this->client->waitForTask(
                $index->name,
                $batchIndexingResponse['taskID'],
            );

            return $document;
        });
    }

    public function delete(Index $index, string $identifier, array $options = []): TaskInterface|null
    {
        $batchIndexingResponse = $this->client->deleteObject($index->name, $identifier);

        if (!($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new AsyncTask(function () use ($batchIndexingResponse, $index) {
            \assert(isset($batchIndexingResponse['taskID']) && \is_int($batchIndexingResponse['taskID']), 'Task ID is expected to be returned by algolia client.');

            $this->client->waitForTask(
                $index->name,
                $batchIndexingResponse['taskID'],
            );
        });
    }

    public function bulk(Index $index, iterable $saveDocuments, iterable $deleteDocumentIdentifiers, int $bulkSize = 100, array $options = []): TaskInterface|null
    {
        $identifierField = $index->getIdentifierField();

        $batchIndexingResponses = [];
        foreach (BulkHelper::splitBulk($saveDocuments, $bulkSize) as $bulkSaveDocuments) {
            $marshalledBulkSaveDocuments = [];
            foreach ($bulkSaveDocuments as $document) {
                $document = $this->marshaller->marshall($index->fields, $document);
                $document['objectID'] = $document[$identifierField->name]; // TODO check objectIDKey instead see: https://github.com/algolia/algoliasearch-client-php/issues/738

                $marshalledBulkSaveDocuments[] = $document;
            }

            $batchIndexingResponses[] = $this->client->saveObjects($index->name, $marshalledBulkSaveDocuments);
        }

        foreach (BulkHelper::splitBulk($deleteDocumentIdentifiers, $bulkSize) as $bulkDeleteDocumentIdentifiers) {
            $batchIndexingResponses[] = $this->client->deleteObjects($index->name, $bulkDeleteDocumentIdentifiers);
        }

        if (!($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new AsyncTask(function () use ($batchIndexingResponses, $index) {
            foreach ($batchIndexingResponses as $batchIndexingResponseList) {
                foreach ($batchIndexingResponseList as $batchIndexingResponse) {
                    \assert(isset($batchIndexingResponse['taskID']) && \is_int($batchIndexingResponse['taskID']), 'Task ID is expected to be returned by algolia client.');

                    $this->client->waitForTask(
                        $index->name,
                        $batchIndexingResponse['taskID'],
                    );
                }
            }
        });
    }
}
