<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\Elasticsearch;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Schranz\Search\SEAL\Adapter\IndexerInterface;
use Schranz\Search\SEAL\Marshaller\Marshaller;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Task\SyncTask;
use Schranz\Search\SEAL\Task\TaskInterface;

final class ElasticsearchIndexer implements IndexerInterface
{
    private readonly Marshaller $marshaller;

    public function __construct(
        private readonly Client $client,
    ) {
        $this->marshaller = new Marshaller();
    }

    public function save(Index $index, array $document, array $options = []): ?TaskInterface
    {
        $identifierField = $index->getIdentifierField();

        /** @var string|int|null $identifier */
        $identifier = $document[$identifierField->name] ?? null;

        $document = $this->marshaller->marshall($index->fields, $document);

        /** @var Elasticsearch $response */
        $response = $this->client->index([
            'index' => $index->name,
            'id' => (string) $identifier,
            'body' => $document,
            // TODO refresh should be refactored with async tasks
            'refresh' => $options['return_slow_promise_result'] ?? false, // update document immediately, so it is available in the `/_search` api directly
        ]);

        if (200 !== $response->getStatusCode() && 201 !== $response->getStatusCode()) {
            throw new \RuntimeException('Unexpected error while indexing document with identifier "' . $identifier . '".');
        }

        if (!($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask($document);
    }

    public function delete(Index $index, string $identifier, array $options = []): ?TaskInterface
    {
        try {
            /** @var Elasticsearch $response */
            $response = $this->client->delete([
                'index' => $index->name,
                'id' => $identifier,
                // TODO refresh should be refactored with async tasks
                'refresh' => $options['return_slow_promise_result'] ?? false, // update document immediately, so it is no longer available in the `/_search` api directly
            ]);

            if (200 !== $response->getStatusCode() && ($response->asArray()['deleted'] ?? false) === false) {
                throw new \RuntimeException('Unexpected error while delete document with identifier "' . $identifier . '".');
            }
        } catch (ClientResponseException $e) {
            if (404 !== $e->getResponse()->getStatusCode()) {
                throw $e;
            }
        }

        if (!($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
    }
}
