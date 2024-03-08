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

interface IndexerInterface
{
    /**
     * @param array<string, mixed> $document
     * @param array{return_slow_promise_result?: true} $options
     *
     * @return ($options is non-empty-array ? TaskInterface<array<string, mixed>> : null)
     */
    public function save(Index $index, array $document, array $options = []): TaskInterface|null;

    /**
     * @param array{return_slow_promise_result?: true} $options
     *
     * @return ($options is non-empty-array ? TaskInterface<void|null> : null)
     */
    public function delete(Index $index, string $identifier, array $options = []): TaskInterface|null;
}
