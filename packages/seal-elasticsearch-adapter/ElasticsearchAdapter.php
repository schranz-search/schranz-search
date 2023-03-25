<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\Elasticsearch;

use Elastic\Elasticsearch\Client;
use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\Adapter\IndexerInterface;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Adapter\SearcherInterface;

final class ElasticsearchAdapter implements AdapterInterface
{
    private readonly SchemaManagerInterface $schemaManager;

    private readonly IndexerInterface $indexer;

    private readonly SearcherInterface $searcher;

    public function __construct(
        Client $client,
        ?SchemaManagerInterface $schemaManager = null,
        ?IndexerInterface $indexer = null,
        ?SearcherInterface $searcher = null,
    ) {
        if ($client->getAsync()) {
            throw new \RuntimeException('Currently only synchronous client is supported.');
        }

        $this->schemaManager = $schemaManager ?? new ElasticsearchSchemaManager($client);
        $this->indexer = $indexer ?? new ElasticsearchIndexer($client);
        $this->searcher = $searcher ?? new ElasticsearchSearcher($client);
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
