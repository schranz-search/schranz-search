<?php

namespace Schranz\Search\SEAL\Adapter\Algolia;

use Algolia\AlgoliaSearch\SearchClient;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Schema\Index;

final class AlgoliaSchemaManager implements SchemaManagerInterface
{
    public function __construct(
        private readonly SearchClient $client,
    ) {
    }

    public function existIndex(Index $index): bool
    {
        $index = $this->client->initIndex($index->name);

        return $index->exists();
    }

    public function dropIndex(Index $index): void
    {
         $index = $this->client->initIndex($index->name);

         $index->delete();
    }

    public function createIndex(Index $index): void
    {
        $searchIndex = $this->client->initIndex($index->name);

        $searchIndex->setSettings([
            'searchableAttributes' => [],
            'attributesForFaceting' => [
                $index->getIdentifierField()->name,
            ],
        ]);
    }
}
