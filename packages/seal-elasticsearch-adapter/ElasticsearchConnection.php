<?php

namespace Schranz\Search\SEAL\Adapter\Elasticsearch;

use Elastic\Elasticsearch\Client;
use Schranz\Search\SEAL\Adapter\ConnectionInterface;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Search\Condition\IdentifierCondition;
use Schranz\Search\SEAL\Search\Result;
use Schranz\Search\SEAL\Search\Search;

final class ElasticsearchConnection implements ConnectionInterface
{
    public function __construct(
        private readonly Client $client,
    ) {
    }

    public function save(Index $index, array $document): array
    {
        // TODO
    }

    public function delete(Index $index, string $identifier): void
    {
        // TODO
    }

    public function search(Search $search): Result
    {
        // TODO
    }
}
