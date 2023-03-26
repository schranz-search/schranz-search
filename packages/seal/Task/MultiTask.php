<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Task;

/**
 * A Task object which waits for multiple tasks.
 *
 * @template-implements TaskInterface<null>
 */
final class MultiTask implements TaskInterface
{
    /**
     * @param TaskInterface<mixed>[] $tasks
     */
    public function __construct(
        private readonly array $tasks,
    ) {
    }

    /**
     * @return null
     */
    public function wait(): mixed
    {
        foreach ($this->tasks as $task) {
            $task->wait();
        }

        return null;
    }
}
