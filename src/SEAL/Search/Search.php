<?php

namespace Schranz\Search\SEAL\Search;

use Schranz\Search\SEAL\Schema\Index;

final class Search
{
    /**
     * @param array<string, Index>
     * @param object[]
     */
    public function __construct(
        public readonly array $indexes = [],
        public readonly array $filters = [],
        public readonly ?int $limit = null,
        public readonly  ?int $offset = null,
    ) {}
}
