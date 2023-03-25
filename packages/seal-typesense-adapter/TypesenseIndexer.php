<?php

namespace Schranz\Search\SEAL\Adapter\Typesense;

use Schranz\Search\SEAL\Adapter\IndexerInterface;
use Schranz\Search\SEAL\Task\SyncTask;
use Typesense\Client;
use Schranz\Search\SEAL\Marshaller\Marshaller;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Task\TaskInterface;

final class TypesenseIndexer implements IndexerInterface
{
    private Marshaller $marshaller;

    public function __construct(
        private readonly Client $client,
    ) {
        $this->marshaller = new Marshaller(dateAsInteger: true);
    }

    public function save(Index $index, array $document, array $options = []): ?TaskInterface
    {
        $identifierField = $index->getIdentifierField();

        /** @var string|null $identifier */
        $identifier = ((string) $document[$identifierField->name]) ?? null;

        $marshalledDocument = $this->marshaller->marshall($index->fields, $document);
        $marshalledDocument['id'] = $identifier;

        $this->client->collections[$index->name]->documents->upsert($marshalledDocument);

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
    }

    public function delete(Index $index, string $identifier, array $options = []): ?TaskInterface
    {
        $this->client->collections[$index->name]->documents[$identifier]->delete();

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
    }
}
