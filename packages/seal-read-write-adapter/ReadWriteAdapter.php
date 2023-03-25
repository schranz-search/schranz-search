<?php

namespace Schranz\Search\SEAL\Adapter\ReadWrite;

use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\Adapter\IndexerInterface;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Adapter\SearcherInterface;

final class ReadWriteAdapter implements AdapterInterface
{
    public function __construct(
        private readonly AdapterInterface $readAdapter,
        private readonly AdapterInterface $writeAdapter,
    ) {}

    public function getSchemaManager(): SchemaManagerInterface
    {
        return $this->writeAdapter->getSchemaManager();
    }

    public function getIndexer(): IndexerInterface
    {
        return $this->writeAdapter->getIndexer();
    }

    public function getSearcher(): SearcherInterface
    {
        return $this->readAdapter->getSearcher();
    }
}
