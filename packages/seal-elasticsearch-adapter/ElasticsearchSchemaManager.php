<?php

namespace Schranz\Search\SEAL\Adapter\Elasticsearch;

use Elastic\Elasticsearch\Client;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Schema\Index;

final class ElasticsearchSchemaManager implements SchemaManagerInterface
{
    public function __construct(
        private readonly Client $client,
    ) {
    }

    public function existIndex(Index $index): bool
    {
    }

    public function dropIndex(Index $index): void
    {
    }

    public function createIndex(Index $index): void
    {
    }
}
