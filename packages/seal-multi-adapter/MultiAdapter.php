<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\Multi;

use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\Adapter\IndexerInterface;
use Schranz\Search\SEAL\Adapter\ReadWrite\MultiIndexer;
use Schranz\Search\SEAL\Adapter\ReadWrite\MultiSchemaManager;
use Schranz\Search\SEAL\Adapter\ReadWrite\MultiSearcher;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Adapter\SearcherInterface;

final class MultiAdapter implements AdapterInterface
{
    private ?SchemaManagerInterface $schemaManager = null;

    private ?IndexerInterface $indexer = null;

    private SearcherInterface $searcher;

    /**
     * @param iterable<AdapterInterface> $adapters
     */
    public function __construct(
        private readonly iterable $adapters,
    ) {
    }

    public function getSchemaManager(): SchemaManagerInterface
    {
        if (!$this->schemaManager instanceof SchemaManagerInterface) {
            $schemaManagers = [];
            foreach ($this->adapters as $adapter) {
                $schemaManagers[] = $adapter->getSchemaManager();
            }

            $this->schemaManager = new MultiSchemaManager($schemaManagers);
        }

        return $this->schemaManager;
    }

    public function getIndexer(): IndexerInterface
    {
        if (!$this->indexer instanceof IndexerInterface) {
            $indexers = [];
            foreach ($this->adapters as $adapter) {
                $indexers[] = $adapter->getIndexer();
            }

            $this->indexer = new MultiIndexer($indexers);
        }

        return $this->indexer;
    }

    public function getSearcher(): SearcherInterface
    {
        if (!$this->indexer instanceof IndexerInterface) {
            $searchers = [];
            foreach ($this->adapters as $adapter) {
                $searchers[] = $adapter->getSearcher();
            }

            $this->searcher = new MultiSearcher($searchers);
        }

        return $this->searcher;
    }
}
