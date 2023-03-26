<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Testing;

use Schranz\Search\SEAL\Task\MultiTask;
use Schranz\Search\SEAL\Task\TaskInterface;

/**
 * @interal
 */
final class TaskHelper
{
    /**
     * @var TaskInterface<mixed>[]
     */
    public array $tasks = [];

    public function waitForAll(): void
    {
        (new MultiTask($this->tasks))->wait();

        $this->tasks = [];
    }
}
