<?php

namespace Schranz\Search\SEAL\Schema\Field;

/**
 * Type to store date and date times.
 */
final class DateTimeField extends AbstractField
{
    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        string $name,
        bool $multiple = false,
        bool $searchable = false,
        bool $filterable = false,
        bool $sortable = false,
        array $options = []
    ) {
        if ($searchable === true) {
            throw new \InvalidArgumentException('Searchability for DateTimeField is not yet implemented: https://github.com/schranz-search/schranz-search/issues/97');
        }

        parent::__construct(
            $name,
            $multiple,
            $searchable,
            $filterable,
            $sortable,
            $options
        );
    }
}
