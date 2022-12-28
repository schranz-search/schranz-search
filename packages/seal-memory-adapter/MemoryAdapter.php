<?php

namespace Schranz\Search\SEAL\Adapter\Memory;

use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\Adapter\ConnectionInterface;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;

final class MemoryAdapter implements AdapterInterface
{
    private readonly ConnectionInterface $connection;

    private readonly SchemaManagerInterface $schemaManager;

    public function __construct(
        ?ConnectionInterface $connection = null,
        ?SchemaManagerInterface $schemaManager = null,
    ) {
        $this->connection = $connection ?? new MemoryConnection();
        $this->schemaManager = $schemaManager ?? new MemorySchemaManager();
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
