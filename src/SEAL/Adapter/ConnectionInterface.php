<?php

namespace Schranz\Search\SEAL\Adapter;

use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Search\Result;
use Schranz\Search\SEAL\Search\Search;

interface ConnectionInterface
{
    public function save(Index $index, array $document);

    public function delete(Index $index, string $identifier);

    public function search(Search $search): Result;
}
