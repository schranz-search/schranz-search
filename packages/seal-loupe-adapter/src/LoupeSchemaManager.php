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

namespace Schranz\Search\SEAL\Adapter\Loupe;

use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Task\SyncTask;
use Schranz\Search\SEAL\Task\TaskInterface;

final class LoupeSchemaManager implements SchemaManagerInterface
{
    public function __construct(
        private readonly LoupeHelper $loupeHelper,
    ) {
    }

    public function existIndex(Index $index): bool
    {
        return $this->loupeHelper->existIndex($index);
    }

    public function dropIndex(Index $index, array $options = []): ?TaskInterface
    {
        $this->loupeHelper->dropIndex($index);

        if (!($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
    }

    public function createIndex(Index $index, array $options = []): ?TaskInterface
    {
        $this->loupeHelper->createIndex($index);
        $this->loupeHelper->getLoupe($index);

        if (!($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
    }
}
