<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter;

use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Task\TaskInterface;

interface SchemaManagerInterface
{
    public function existIndex(Index $index): bool;

    /**
     * @template T of bool
     *
     * @param array{return_slow_promise_result?: T} $options
     *
     * @return (T is true ? TaskInterface<null|void> : null)
     */
    public function dropIndex(Index $index, array $options = []): ?TaskInterface;

    /**
     * @template T of bool
     *
     * @param array{return_slow_promise_result?: T} $options
     *
     * @return (T is true ? TaskInterface<null|void> : null)
     */
    public function createIndex(Index $index, array $options = []): ?TaskInterface;
}
