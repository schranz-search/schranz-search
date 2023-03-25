<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Search\Condition;

class SearchCondition
{
    public function __construct(
        public readonly string $query,
    ) {
    }
}
