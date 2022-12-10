<?php

namespace Schranz\Search\SEAL\Adapter;

use Schranz\Search\SEAL\Search\Result;
use Schranz\Search\SEAL\Search\Search;

interface AdapterInterface
{
    public function getSchemaManager(): SchemaManagerInterface;

    public function getConnection(): ConnectionInterface;

    /**
     * @return iterable<array<string, mixed>>
     */
    public function search(Search $searchBuilder): Result;
}
