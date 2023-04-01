<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter;

use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Task\TaskInterface;

interface IndexerInterface
{
    /**
     * @param array<string, mixed> $document
     * @param array{return_slow_promise_result?: true} $options
     *
     * @return ($options is non-empty-array ? TaskInterface<array<string, mixed>> : null)
     */
    public function save(Index $index, array $document, array $options = []): ?TaskInterface;

    /**
     * @param array{return_slow_promise_result?: true} $options
     *
     * @return ($options is non-empty-array ? TaskInterface<null|void> : null)
     */
    public function delete(Index $index, string $identifier, array $options = []): ?TaskInterface;
}
