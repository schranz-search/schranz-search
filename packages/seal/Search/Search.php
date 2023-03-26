<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Search;

use Schranz\Search\SEAL\Schema\Index;

final class Search
{
    /**
     * @param array<string, Index> $indexes
     * @param object[] $filters
     * @param array<string, 'asc'|'desc'> $sortBys
     */
    public function __construct(
        public readonly array $indexes = [],
        public readonly array $filters = [],
        public readonly array $sortBys = [],
        public readonly ?int $limit = null,
        public readonly int $offset = 0,
    ) {
    }
}
