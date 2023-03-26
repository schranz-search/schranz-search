<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\ReadWrite;

use Schranz\Search\SEAL\Adapter\IndexerInterface;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Task\AsyncTask;
use Schranz\Search\SEAL\Task\MultiTask;
use Schranz\Search\SEAL\Task\TaskInterface;

/**
 * @internal this class should never be needed to be instanced manually
 */
final class MultiIndexer implements IndexerInterface
{
    /**
     * @param iterable<IndexerInterface> $indexers
     */
    public function __construct(
        public readonly iterable $indexers,
    ) {
    }

    public function save(Index $index, array $document, array $options = []): ?TaskInterface
    {
        $tasks = [];
        foreach ($this->indexers as $indexer) {
            $task = $indexer->save($index, $document, $options);

            if ($task instanceof TaskInterface) {
                $tasks[] = $task;
            }
        }

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new AsyncTask(function () use ($tasks, $document) {
            $multiTask = new MultiTask($tasks);
            $multiTask->wait();

            return $document;
        });
    }

    public function delete(Index $index, string $identifier, array $options = []): ?TaskInterface
    {
        $tasks = [];
        foreach ($this->indexers as $indexer) {
            $task = $indexer->delete($index, $identifier, $options);

            if ($task instanceof TaskInterface) {
                $tasks[] = $task;
            }
        }

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new AsyncTask(function () use ($tasks): void {
            $multiTask = new MultiTask($tasks);
            $multiTask->wait();
        });
    }
}
