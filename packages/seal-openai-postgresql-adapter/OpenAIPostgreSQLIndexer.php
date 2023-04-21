<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\OpenAIPostgreSQL;

use OpenAI\Client;
use Schranz\Search\SEAL\Adapter\IndexerInterface;
use Schranz\Search\SEAL\Marshaller\FlattenMarshaller;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Task\SyncTask;
use Schranz\Search\SEAL\Task\TaskInterface;

final class OpenAIPostgreSQLIndexer implements IndexerInterface
{
    private readonly FlattenMarshaller $marshaller;

    public function __construct(
        private readonly Client $openAiClient,
        private readonly \PDO $pdoClient,
    ) {
        $this->marshaller = new FlattenMarshaller();
    }

    public function save(Index $index, array $document, array $options = []): ?TaskInterface
    {
        $identifierField = $index->getIdentifierField();

        /** @var string|int|null $identifier */
        $identifier = $document[$identifierField->name] ?? null;

        $document = $this->marshaller->marshall($index->fields, $document);

        /** @var \PDOStatement $statement */
        $statement = $this->pdoClient->prepare('INSERT INTO ' . $index->name . ' (identifier, document) VALUES (:identifier, :document)');
        $statement->execute([
            'identifier' => $identifier,
            'document' => \json_encode($document, \JSON_THROW_ON_ERROR),
        ]);

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask($document);
    }

    public function delete(Index $index, string $identifier, array $options = []): ?TaskInterface
    {
        /** @var \PDOStatement $statement */
        $statement = $this->pdoClient->prepare('DELETE FROM ' . $index->name . ' WHERE identifier = :identifier');
        $statement->execute([
            'identifier' => $identifier,
        ]);

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
    }
}
