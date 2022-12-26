<?php

namespace Schranz\Search\SEAL\Adapter;

use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\DSL\Result;
use Schranz\Search\SEAL\DSL\Search;

interface ConnectionInterface
{
    /**
     * @return array<string, mixed>
     */
    public function save(Index $index, array $document): array;

    public function delete(Index $index, string $identifier): void;

    public function search(Search $search): Result;
}
