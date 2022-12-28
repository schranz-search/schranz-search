<?php

namespace Schranz\Search\SEAL\Adapter\Memory;

use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Schema\Index;

final class MemorySchemaManager implements SchemaManagerInterface
{
    private array $indexes = [];

    public function existIndex(Index $index): bool
    {
        return array_key_exists($index->name, $this->indexes);
    }

    public function dropIndex(Index $index): void
    {
        unset($this->indexes[$index->name]);
    }

    public function createIndex(Index $index): void
    {
        $this->indexes[$index->name] = $index;
    }
}
