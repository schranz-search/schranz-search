<?php

namespace Schranz\Search\SEAL\Adapter\Memory;

use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\Adapter\IndexerInterface;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Adapter\SearcherInterface;

final class MemoryAdapter implements AdapterInterface
{
    private readonly SchemaManagerInterface $schemaManager;

    private readonly IndexerInterface $indexer;

    private readonly SearcherInterface $searcher;

    public function __construct(
        ?SchemaManagerInterface $schemaManager = null,
        ?IndexerInterface $indexer = null,
        ?SearcherInterface $searcher = null,
    ) {
        $this->schemaManager = $schemaManager ?? new MemorySchemaManager();
        $this->indexer = $indexer ?? new MemoryIndexer();
        $this->searcher = $searcher ?? new MemorySearcher();
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
