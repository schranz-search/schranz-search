<?php

namespace Schranz\Search\SEAL\Task;

/**
 * An easier to use Task object for synchronous tasks.
 *
 * @template-covariant T of mixed
 *
 * @template-implements TaskInterface<T>
 */
final class SyncTask implements TaskInterface
{
    /**
     * @param T $result
     */
    public function __construct(
        private readonly mixed $result,
    ) {}

    public function wait(): mixed
    {
        return $this->result;
    }
}
