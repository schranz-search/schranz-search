<?php

namespace Schranz\Search\SEAL\Adapter;

use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Search\Result;
use Schranz\Search\SEAL\Search\Search;
use Schranz\Search\SEAL\Task\TaskInterface;

interface ConnectionInterface
{
    /**
     * @template T of bool
     *
     * @param array{return_slow_promise_result: T} $options
     *
     * @return (T is true ? TaskInterface : null)
     */
    public function save(Index $index, array $document, array $options = []): ?TaskInterface;

    /**
     * @template T of bool
     *
     * @param array{return_slow_promise_result: T} $options
     *
     * @return (T is true ? TaskInterface : null)
     */
    public function delete(Index $index, string $identifier, array $options = []): ?TaskInterface;

    public function search(Search $search): Result;
}
