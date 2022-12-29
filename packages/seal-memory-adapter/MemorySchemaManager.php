<?php

namespace Schranz\Search\SEAL\Adapter\Memory;

use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Schema\Index;

final class MemorySchemaManager implements SchemaManagerInterface
{

    public function existIndex(Index $index): bool
    {
        return MemoryStorage::existIndex($index);
    }

    public function dropIndex(Index $index): void
    {
        MemoryStorage::dropIndex($index);
    }

    public function createIndex(Index $index): void
    {
        MemoryStorage::createIndex($index);
    }
}
