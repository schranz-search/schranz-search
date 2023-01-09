<?php

namespace Schranz\Search\SEAL\Search\Condition;

class LowerThanEqualCondition
{
    public function __construct(
        public readonly string $field,
        public readonly string|int|float|bool $value,
    ) {
    }
}
