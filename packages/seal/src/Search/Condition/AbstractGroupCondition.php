<?php

declare(strict_types=1);

/*
 * This file is part of the Schranz Search package.
 *
 * (c) Alexander Schranz <alexander@sulu.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Schranz\Search\SEAL\Search\Condition;

abstract class AbstractGroupCondition
{
    /**
     * @var array<EqualCondition|GreaterThanCondition|GreaterThanEqualCondition|IdentifierCondition|LessThanCondition|LessThanEqualCondition|NotEqualCondition|AndCondition|OrCondition>
     */
    private array $conditions = [];

    /**
     * @param EqualCondition|GreaterThanCondition|GreaterThanEqualCondition|IdentifierCondition|LessThanCondition|LessThanEqualCondition|NotEqualCondition|AndCondition|OrCondition $conditions
     */
    public function __construct(...$conditions)
    {
        foreach ($conditions as $condition) {
            $this->addCondition($condition);
        }
    }

    public function addCondition(EqualCondition|GreaterThanCondition|GreaterThanEqualCondition|IdentifierCondition|LessThanCondition|LessThanEqualCondition|NotEqualCondition|AndCondition|OrCondition $condition): static
    {
        $this->conditions[] = $condition;

        return $this;
    }

    /**
     * @return array<EqualCondition|GreaterThanCondition|GreaterThanEqualCondition|IdentifierCondition|LessThanCondition|LessThanEqualCondition|NotEqualCondition|AndCondition|OrCondition>
     */
    public function getConditions(): array
    {
        return $this->conditions;
    }
}
