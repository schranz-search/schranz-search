<?php

declare(strict_types=1);

/*
 * This file is part of the Schranz Search package.
 *
 * (c) Alexander Schranz <alexander@sulu.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        Client $client,
        SchemaManagerInterface|null $schemaManager = null,
        IndexerInterface|null $indexer = null,
        SearcherInterface|null $searcher = null,
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
