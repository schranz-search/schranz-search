<?php

namespace Schranz\Search\SEAL\Adapter\RediSearch;

use Redis;
use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\Adapter\ConnectionInterface;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;

final class RediSearchAdapter implements AdapterInterface
{
    private readonly ConnectionInterface $connection;

    private readonly SchemaManagerInterface $schemaManager;

    public function __construct(
        private readonly Redis $client,
        ?ConnectionInterface $connection = null,
        ?SchemaManagerInterface $schemaManager = null,
    ) {
        $this->connection = $connection ?? new RediSearchConnection($client);
        $this->schemaManager = $schemaManager ?? new RediSearchSchemaManager($client);
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
