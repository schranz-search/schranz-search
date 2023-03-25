<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\Algolia;

use Algolia\AlgoliaSearch\SearchClient;
use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\Adapter\IndexerInterface;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Adapter\SearcherInterface;

final class AlgoliaAdapter implements AdapterInterface
{
    private readonly SchemaManagerInterface $schemaManager;

    private readonly IndexerInterface $indexer;

    private readonly SearcherInterface $searcher;

    public function __construct(
        SearchClient $client,
        ?SchemaManagerInterface $schemaManager = null,
        ?IndexerInterface $indexer = null,
        ?SearcherInterface $searcher = null,
    ) {
        $this->schemaManager = $schemaManager ?? new AlgoliaSchemaManager($client);
        $this->indexer = $indexer ?? new AlgoliaIndexer($client);
        $this->searcher = $searcher ?? new AlgoliaSearcher($client);
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
