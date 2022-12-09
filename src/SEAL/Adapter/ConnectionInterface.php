<?php

namespace Schranz\Search\SEAL\Adapter;

use Schranz\Search\SEAL\Schema\Index;

interface ConnectionInterface
{
    public function index(Index $index, array $document);

    public function delete(Index $index, string $identifier);
}
