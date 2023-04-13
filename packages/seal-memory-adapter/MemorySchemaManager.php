<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\Memory;

use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Task\SyncTask;
use Schranz\Search\SEAL\Task\TaskInterface;

final class MemorySchemaManager implements SchemaManagerInterface
{
    public function existIndex(Index $index): bool
    {
        return MemoryStorage::existIndex($index);
    }

    public function dropIndex(Index $index, array $options = []): ?TaskInterface
    {
        MemoryStorage::dropIndex($index);

        if (!($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
    }

    public function createIndex(Index $index, array $options = []): ?TaskInterface
    {
        MemoryStorage::createIndex($index);

        if (!($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
    }
}
