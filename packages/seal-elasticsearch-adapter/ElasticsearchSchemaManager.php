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
        $response = $this->client->indices()->exists([
            'index' => $index->name,
        ]);

        return $response->getStatusCode() !== 404;
    }

    public function dropIndex(Index $index): void
    {
        $response = $this->client->indices()->delete([
            'index' => $index->name,
        ]);
    }

    public function createIndex(Index $index): void
    {
        $this->client->indices()->create([
            'index' => $index->name,
            'body' => [
                'mappings' => [
                    'properties' => [
                        // TODO mapping
                    ],
                ],
            ],
        ]);
    }
}
