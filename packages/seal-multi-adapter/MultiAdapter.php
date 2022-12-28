<?php

namespace Schranz\Search\SEAL\Adapter\Multi;

use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\Adapter\ConnectionInterface;
use Schranz\Search\SEAL\Adapter\ReadWrite\MultiConnection;
use Schranz\Search\SEAL\Adapter\ReadWrite\MultiSchemaManager;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;

final class MultiAdapter implements AdapterInterface
{
    private ?ConnectionInterface $connection = null;

    private ?SchemaManagerInterface $schemaManager = null;

    /**
     * @param iterable<AdapterInterface> $adapters
     */
    public function __construct(
        private readonly iterable $adapters,
    ) {}

    public function getSchemaManager(): SchemaManagerInterface
    {
        if ($this->schemaManager === null) {
            $schemaManagers = [];
            foreach ($this->adapters as $adapter) {
                $schemaManagers[] = $adapter->getSchemaManager();
            }

            $this->schemaManager = new MultiSchemaManager($schemaManagers);
        }

        return $this->schemaManager;
    }

    public function getConnection(): ConnectionInterface
    {
        if ($this->connection === null) {
            $connections = [];
            foreach ($this->adapters as $adapter) {
                $connections[] = $adapter->getConnection();
            }

            $this->connection = new MultiConnection($connections);
        }

        return $this->connection;
    }
}
