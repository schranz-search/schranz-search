<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\OpenAIPostgreSQL;

use OpenAI\Client;
use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\Adapter\IndexerInterface;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Adapter\SearcherInterface;

final class OpenAIPostgreSQLAdapter implements AdapterInterface
{
    private readonly SchemaManagerInterface $schemaManager;

    private readonly IndexerInterface $indexer;

    private readonly SearcherInterface $searcher;

    public function __construct(
        Client $openAiClient,
        \PDO $pdoClient,
        ?SchemaManagerInterface $schemaManager = null,
        ?IndexerInterface $indexer = null,
        ?SearcherInterface $searcher = null,
    ) {
        $this->schemaManager = $schemaManager ?? new OpenAIPostgreSQLSchemaManager($openAiClient, $pdoClient);
        $this->indexer = $indexer ?? new OpenAIPostgreSQLIndexer($openAiClient, $pdoClient);
        $this->searcher = $searcher ?? new OpenAIPostgreSQLSearcher($openAiClient, $pdoClient);
    }

    public function getSchemaManager(): SchemaManagerInterface
    {
        return $this->schemaManager;
    }

    public function getIndexer(): IndexerInterface
    {
        return $this->indexer;
    }

    public function getSearcher(): SearcherInterface
    {
        return $this->searcher;
    }
}
