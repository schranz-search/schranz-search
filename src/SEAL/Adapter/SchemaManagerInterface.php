<?php

namespace Schranz\Search\SEAL\Adapter;

use Schranz\Search\SEAL\Schema\Index;

interface SchemaManagerInterface
{
    public function existIndex(Index $index): void;

    public function dropIndex(Index $index): void;

    public function createIndex(Index $index): void;
}
