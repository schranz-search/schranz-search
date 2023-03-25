<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\Memory;

use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\Adapter\IndexerInterface;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Adapter\SearcherInterface;

final class MemoryAdapter implements AdapterInterface
{
    public function __construct(
        private readonly SchemaManagerInterface $schemaManager = new MemorySchemaManager(),
        private readonly IndexerInterface $indexer = new MemoryIndexer(),
        private readonly SearcherInterface $searcher = new MemorySearcher()
    ) {
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
