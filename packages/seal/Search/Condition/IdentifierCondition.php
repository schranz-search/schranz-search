<?php

namespace Schranz\Search\SEAL\Search\Condition;

class IdentifierCondition
{
    public function __construct(
        readonly string $identifier,
    ) {
    }
}
