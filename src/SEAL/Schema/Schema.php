<?php

namespace Schranz\Search\SEAL\Schema;

final class Schema
{
    /**
     * @param array<string, Index>
     */
    public function __construct(
        public readonly array $indexes
    ) {}
}
