<?php

namespace Schranz\Search\SEAL\Adapter\ReadWrite;

use Schranz\Search\SEAL\Adapter\ConnectionInterface;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Search\Result;
use Schranz\Search\SEAL\Search\Search;

/**
 * @internal This class should never be needed to be instanced manually.
 */
final class MultiSchemaManager implements SchemaManagerInterface
{
    /**
     * @param iterable<SchemaManagerInterface> $schemaManagers
     */
    public function __construct(
        public readonly iterable $schemaManagers,
    ) {}

    public function existIndex(Index $index): bool
    {
        $existIndex = true;
        foreach ($this->schemaManagers as $schemaManager) {
            $existIndex = $existIndex && $schemaManager->existIndex($index);
        }

        return $existIndex;
    }

    public function dropIndex(Index $index): void
    {
        foreach ($this->schemaManagers as $schemaManager) {
            $schemaManager->dropIndex($index);
        }
    }

    public function createIndex(Index $index): void
    {
        foreach ($this->schemaManagers as $schemaManager) {
            $schemaManager->createIndex($index);
        }
    }
}
