<?php

namespace Schranz\Search\SEAL\Adapter\Meilisearch;

use Meilisearch\Client;
use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\Adapter\IndexerInterface;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Adapter\SearcherInterface;

final class MeilisearchAdapter implements AdapterInterface
{

    private readonly SchemaManagerInterface $schemaManager;

    private readonly IndexerInterface $indexer;

    private readonly SearcherInterface $searcher;

    public function __construct(
        private readonly Client $client,
        ?SchemaManagerInterface $schemaManager = null,
        ?IndexerInterface $indexer = null,
        ?SearcherInterface $searcher = null,
    ) {
        $this->schemaManager = $schemaManager ?? new MeilisearchSchemaManager($client);
        $this->indexer = $indexer ?? new MeilisearchIndexer($client);
        $this->searcher = $searcher ?? new MeilisearchSearcher($client);
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
