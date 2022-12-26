<?php

namespace Schranz\Search\SEAL\DSL\Condition;

class IdentifierCondition
{
    public function __construct(
        readonly string $identifier,
    ) {
    }
}
