<?php

namespace Schranz\Search\SEAL\Adapter\Elasticsearch;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Schranz\Search\SEAL\Adapter\IndexerInterface;
use Schranz\Search\SEAL\Marshaller\Marshaller;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Task\SyncTask;
use Schranz\Search\SEAL\Task\TaskInterface;

final class ElasticsearchIndexer implements IndexerInterface
{
    private Marshaller $marshaller;

    public function __construct(
        private readonly Client $client,
    ) {
        $this->marshaller = new Marshaller();
    }

    public function save(Index $index, array $document, array $options = []): ?TaskInterface
    {
        $identifierField = $index->getIdentifierField();

        /** @var string|null $identifier */
        $identifier = ((string) $document[$identifierField->name]) ?? null;

        $document = $this->marshaller->marshall($index->fields, $document);

        $response = $this->client->index([
            'index' => $index->name,
            'id' => $identifier,
            'body' => $document,
            // TODO refresh should be refactored with async tasks
            'refresh' => $options['return_slow_promise_result'] ?? false, // update document immediately, so it is available in the `/_search` api directly
        ]);

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        $document[$identifierField->name] = $response->asArray()['_id'];

        return new SyncTask($document);
    }

    public function delete(Index $index, string $identifier, array $options = []): ?TaskInterface
    {
        try {
            $response = $this->client->delete([
                'index' => $index->name,
                'id' => $identifier,
                // TODO refresh should be refactored with async tasks
                'refresh' => $options['return_slow_promise_result'] ?? false, // update document immediately, so it is no longer available in the `/_search` api directly
            ]);

            if ($response->getStatusCode() !== 200 && ($response->asArray()['deleted'] ?? false) === false) {
                throw new \RuntimeException('Unexpected error while delete document with identifier "' . $identifier . '".');
            }
        } catch (ClientResponseException $e) {
            if ($e->getResponse()->getStatusCode() !== 404) {
                throw $e;
            }
        }

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
    }
}
