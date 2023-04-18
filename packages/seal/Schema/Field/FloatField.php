<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Schema\Field;

/**
 * Type to store any PHP float value.
 *
 * @property false $searchable
 *
 * @readonly
 */
final class FloatField extends AbstractField
{
    /**
     * @param false $searchable
     * @param array<string, mixed> $options
     */
    public function __construct(
        string $name,
        bool $multiple = false,
        bool $searchable = false,
        bool $filterable = false,
        bool $sortable = false,
        array $options = [],
    ) {
        if ($searchable) { // @phpstan-ignore-line
            throw new \InvalidArgumentException('Searchability for FloatField is not yet implemented: https://github.com/schranz-search/schranz-search/issues/97');
        }

        parent::__construct(
            $name,
            $multiple,
            $searchable,
            $filterable,
            $sortable,
            $options,
        );
    }
}
