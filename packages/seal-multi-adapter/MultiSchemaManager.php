<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\ReadWrite;

use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Task\AsyncTask;
use Schranz\Search\SEAL\Task\MultiTask;
use Schranz\Search\SEAL\Task\TaskInterface;

/**
 * @internal this class should never be needed to be instanced manually
 */
final class MultiSchemaManager implements SchemaManagerInterface
{
    /**
     * @param iterable<SchemaManagerInterface> $schemaManagers
     */
    public function __construct(
        public readonly iterable $schemaManagers,
    ) {
    }

    public function existIndex(Index $index): bool
    {
        $existIndex = true;
        foreach ($this->schemaManagers as $schemaManager) {
            $existIndex = $existIndex && $schemaManager->existIndex($index);
        }

        return $existIndex;
    }

    public function dropIndex(Index $index, array $options = []): ?TaskInterface
    {
        $tasks = [];

        foreach ($this->schemaManagers as $schemaManager) {
            $task = $schemaManager->dropIndex($index, $options);

            if ($task instanceof TaskInterface) {
                $tasks[] = $task;
            }
        }

        if (!($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new AsyncTask(function () use ($tasks): void {
            $multiTask = new MultiTask($tasks);
            $multiTask->wait();
        });
    }

    public function createIndex(Index $index, array $options = []): ?TaskInterface
    {
        $tasks = [];
        foreach ($this->schemaManagers as $schemaManager) {
            $task = $schemaManager->createIndex($index, $options);

            if ($task instanceof TaskInterface) {
                $tasks[] = $task;
            }
        }

        if (!($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new AsyncTask(function () use ($tasks): void {
            $multiTask = new MultiTask($tasks);
            $multiTask->wait();
        });
    }
}
