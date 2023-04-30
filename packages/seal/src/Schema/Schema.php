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

namespace Schranz\Search\SEAL\Schema;

/**
 * @readonly
 */
final class Schema
{
    /**
     * @param array<string, Index> $indexes
     */
    public function __construct(
        public readonly array $indexes,
    ) {
    }
}
