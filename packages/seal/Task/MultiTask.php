<?php

namespace Schranz\Search\SEAL\Task;

/**
 * A Task object which waits for multiple tasks.
 *
 * @template-implements TaskInterface<null>
 */
final class MultiTask implements TaskInterface
{
    public function __construct(
        private array $tasks,
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
