<?php

namespace Schranz\Search\SEAL\Adapter\RediSearch;

use Redis;
use Schranz\Search\SEAL\Adapter\IndexerInterface;
use Schranz\Search\SEAL\Marshaller\Marshaller;
use Schranz\Search\SEAL\Task\SyncTask;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Task\TaskInterface;

final class RediSearchIndexer implements IndexerInterface
{
    private Marshaller $marshaller;

    public function __construct(
        private readonly Redis $client,
    ) {
        $this->marshaller = new Marshaller();
    }

    public function save(Index $index, array $document, array $options = []): ?TaskInterface
    {
        $identifierField = $index->getIdentifierField();

        /** @var string|null $identifier */
        $identifier = ((string) $document[$identifierField->name]) ?? null;

        $marshalledDocument = $this->marshaller->marshall($index->fields, $document);

        $jsonSet = $this->client->rawCommand(
            'JSON.SET',
            $index->name . ':' . $identifier,
            '$',
            json_encode($marshalledDocument),
        );

        if ($jsonSet === false) {
            throw $this->createRedisLastErrorException();
        }

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
    }

    public function delete(Index $index, string $identifier, array $options = []): ?TaskInterface
    {
        $jsonDel = $this->client->rawCommand(
            'JSON.DEL',
            $index->name . ':' . $identifier,
        );

        if ($jsonDel === false) {
            throw $this->createRedisLastErrorException();
        }

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
    }

    private function createRedisLastErrorException(): \RuntimeException
    {
        $lastError = $this->client->getLastError();
        $this->client->clearLastError();

        return new \RuntimeException('Redis: ' . $lastError);
    }
}
