<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\Algolia;

use Algolia\AlgoliaSearch\SearchClient;
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
        $this->marshaller = new Marshaller();
    }

    public function save(Index $index, array $document, array $options = []): ?TaskInterface
    {
        $identifierField = $index->getIdentifierField();

        $searchIndex = $this->client->initIndex($index->name);

        $batchIndexingResponse = $searchIndex->saveObject(
            $this->marshaller->marshall($index->fields, $document),
            ['objectIDKey' => $identifierField->name],
        );

        if (!($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new AsyncTask(function () use ($batchIndexingResponse, $document) {
            $batchIndexingResponse->wait();

            return $document;
        });
    }

    public function delete(Index $index, string $identifier, array $options = []): ?TaskInterface
    {
        $searchIndex = $this->client->initIndex($index->name);

        $batchIndexingResponse = $searchIndex->deleteObject($identifier);

        if (!($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new AsyncTask(function () use ($batchIndexingResponse) {
            $batchIndexingResponse->wait();
        });
    }
}
