<?php

namespace Schranz\Search\SEAL\Schema\Field;

/**
 * Type to store any PHP int value.
 */
final class IntegerField extends AbstractField
{
    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        string $name,
        bool $multiple = false,
        bool $searchable = true,
        bool $filterable = false,
        bool $sortable = false,
        array $options = []
    ) {
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
