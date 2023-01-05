<?php

namespace Schranz\Search\SEAL\Task;

/**
 * A Task object for asynchronous tasks.
 *
 * As example Algolia returns us just a task id, which we can use to wait for the task to finish.
 *
 * @template-covariant T of mixed
 *
 * @template-implements TaskInterface<T>
 */
final class AsyncTask implements TaskInterface
{
    /**
     * @param \Closure(): T $callback
     */
    public function __construct(
        \Closure $callback,
    ) {

    }

    public function wait(): mixed
    {
        throw new \RuntimeException('TODO need to be implemented');
    }
}
