<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\OpenAIPostgreSQL;

use OpenAI\Client;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Task\SyncTask;
use Schranz\Search\SEAL\Task\TaskInterface;

final class OpenAIPostgreSQLSchemaManager implements SchemaManagerInterface
{
    public function __construct(
        private readonly Client $openAiClient,
        private readonly \PDO $pdoClient,
    ) {
    }

    public function existIndex(Index $index): bool
    {
        $statement = $this->pdoClient->query(
            <<<SQL
            SELECT EXISTS (
                SELECT FROM 
                    pg_tables
                WHERE 
                    schemaname = 'public' AND 
                    tablename  = '{$index->name}'
            );
            SQL
        );

        /** @var bool $exists */
        $exists = $statement->fetchColumn();

        return $exists;
    }

    public function dropIndex(Index $index, array $options = []): ?TaskInterface
    {
        $this->pdoClient->exec(
            <<<SQL
                DROP TABLE {$index->name}
            SQL
        );

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
    }

    public function createIndex(Index $index, array $options = []): ?TaskInterface
    {
        $this->pdoClient->exec(
            <<<SQL
            CREATE EXTENSION IF NOT EXISTS vector;
            SQL
        );

        $this->pdoClient->exec(
            <<<SQL
            CREATE TABLE IF NOT EXISTS {$index->name} (
                identifier VARCHAR(48) PRIMARY KEY,
                document JSONB,
                embedding vector(1536) 
            );
            SQL
        ); // OpenAI's text-embedding-ada-002 model outputs 1536 dimensions, so we will use that for our vector size.

        // TODO optimize index: https://github.com/pgvector/pgvector/tree/v0.4.1#indexing
        // TODO make a filterable columns

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
    }
}
