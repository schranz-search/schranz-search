<?php

namespace Schranz\Search\SEAL\Search\Condition;

class OrCondition
{
    /**
     * @var array<object>
     */
    public function __construct(public readonly array $conditions)
    {
    }
}