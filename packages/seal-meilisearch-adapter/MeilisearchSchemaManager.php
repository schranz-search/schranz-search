<?php

namespace Schranz\Search\SEAL\Adapter\Meilisearch;

use Meilisearch\Client;
use Meilisearch\Exceptions\ApiException;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Schema\Index;

final class MeilisearchSchemaManager implements SchemaManagerInterface
{
    public function __construct(
        private readonly Client $client,
    ) {
    }

    public function existIndex(Index $index): bool
    {
        try {
            $this->client->getRawIndex($index->name);
        } catch (ApiException $e) {
            if ($e->httpStatus !== 404) {
                throw $e;
            }

            return false;
        }

        return true;
    }

    public function dropIndex(Index $index): void
    {
         $this->client->deleteIndex($index->name);
    }

    public function createIndex(Index $index): void
    {
        $this->client->createIndex(
            $index->name,
            [
                'primaryKey' => $index->getIdentifierField()->name,
            ]
        );

        $this->client->index($index->name)
            ->updateSettings([
                'filterableAttributes' => [
                    $index->getIdentifierField()->name
                ],
            ]);
    }
}
