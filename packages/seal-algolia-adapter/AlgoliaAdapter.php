<?php

namespace Schranz\Search\SEAL\Adapter\Algolia;

use Algolia\AlgoliaSearch\SearchClient;
use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\Adapter\ConnectionInterface;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;

final class AlgoliaAdapter implements AdapterInterface
{
    private readonly ConnectionInterface $connection;

    private readonly SchemaManagerInterface $schemaManager;

    public function __construct(
        private readonly SearchClient $client,
        ?ConnectionInterface $connection = null,
        ?SchemaManagerInterface $schemaManager = null,
    ) {
        $this->connection = $connection ?? new AlgoliaConnection($client);
        $this->schemaManager = $schemaManager ?? new AlgoliaSchemaManager($client);
    }

    public function getSchemaManager(): SchemaManagerInterface
    {
        return $this->schemaManager;
    }

    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }
}
