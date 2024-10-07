<?php

namespace Schranz\Search\SEAL\Search\Condition;

class AndCondition
{
    /**
     * @var array<object>
     */
    public function __construct(public readonly array $conditions)
    {
    }
}