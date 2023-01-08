<?php

namespace Schranz\Search\SEAL\Search\Condition;

class EqualCondition
{
    public function __construct(
        public readonly string $field,
        public readonly string|int|float|bool $value,
    ) {
    }
}
