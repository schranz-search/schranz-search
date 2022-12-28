<?php

namespace Schranz\Search\SEAL\Adapter\ReadWrite;

use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\Adapter\ConnectionInterface;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;

final class ReadWriteAdapter implements AdapterInterface
{
    private ?ConnectionInterface $connection = null;

    public function __construct(
        private readonly AdapterInterface $readAdapter,
        private readonly AdapterInterface $writeAdapter,
    ) {}

    public function getSchemaManager(): SchemaManagerInterface
    {
        return $this->writeAdapter->getSchemaManager();
    }

    public function getConnection(): ConnectionInterface
    {
        if ($this->connection === null) {
            $this->connection = new ReadWriteConnection(
                $this->readAdapter->getConnection(),
                $this->writeAdapter->getConnection(),
            );
        }

        return $this->connection;
    }
}
