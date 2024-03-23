<?php

declare(strict_types=1);

/*
 * This file is part of the Schranz Search package.
 *
 * (c) Alexander Schranz <alexander@sulu.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Schranz\Search\SEAL\Adapter;

use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Task\TaskInterface;

interface ReindexableSchemaManagerInterface
{
    /**
     * @template T of bool
     *
     * @param iterable<array<string, mixed>> $documents
     * @param array{return_slow_promise_result?: T} $options
     *
     * @return (T is true ? TaskInterface<void|null> : null)
     */
    public function reindex(Index $index, iterable $documents, array $options = []): ?TaskInterface;
}
