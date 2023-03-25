<?php

namespace Schranz\Search\SEAL\Adapter\Opensearch;

use OpenSearch\Client;
use Schranz\Search\SEAL\Adapter\IndexerInterface;
use Schranz\Search\SEAL\Marshaller\Marshaller;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Task\SyncTask;
use Schranz\Search\SEAL\Task\TaskInterface;

final class OpensearchIndexer implements IndexerInterface
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

        $data = $this->client->index([
            'index' => $index->name,
            'id' => $identifier,
            'body' => $document,
            // TODO refresh should be refactored with async tasks
            'refresh' => $options['return_slow_promise_result'] ?? false, // update document immediately, so it is available in the `/_search` api directly
        ]);

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        $document[$identifierField->name] = $data['_id'];

        return new SyncTask($document);
    }

    public function delete(Index $index, string $identifier, array $options = []): ?TaskInterface
    {
        $data = $this->client->delete([
            'index' => $index->name,
            'id' => $identifier,
            // TODO refresh should be refactored with async tasks
            'refresh' => $options['return_slow_promise_result'] ?? false, // update document immediately, so it is no longer available in the `/_search` api directly
        ]);

        if ($data['result'] !== 'deleted') {
            throw new \RuntimeException('Unexpected error while delete document with identifier "' . $identifier . '" from Index "' . $index->name . '".');
        }

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
    }
}
