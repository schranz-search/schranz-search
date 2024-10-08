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

interface BulkableIndexerInterface
{
    /**
     * @param iterable<array<string, mixed>> $saveDocuments
     * @param iterable<string> $deleteDocumentIdentifiers
     * @param array{return_slow_promise_result?: true} $options
     *
     * @return ($options is non-empty-array ? TaskInterface<void|null> : null)
     */
    public function bulk(
        Index $index,
        iterable $saveDocuments,
        iterable $deleteDocumentIdentifiers,
        int $bulkSize = 100,
        array $options = [],
    ): TaskInterface|null;
}
