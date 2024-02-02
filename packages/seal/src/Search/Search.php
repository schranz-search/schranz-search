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

namespace Schranz\Search\SEAL\Search;

use Schranz\Search\SEAL\Schema\Index;

final class Search
{
    /**
     * @param array<string, Index> $indexes
     * @param object[] $filters
     * @param array<string, 'asc'|'desc'> $sortBys
     * @param array<string> $higlightFields
     */
    public function __construct(
        public readonly array $indexes = [],
        public readonly array $filters = [],
        public readonly array $sortBys = [],
        public readonly ?int $limit = null,
        public readonly int $offset = 0,
        public readonly array $higlightFields = [],
        public readonly string $highlightPreTag = '<mark>',
        public readonly string $highlightPostTag = '</mark>',
    ) {
    }
}
