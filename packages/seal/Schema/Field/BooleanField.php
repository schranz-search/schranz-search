<?php

namespace Schranz\Search\SEAL\Schema\Field;

/**
 * Type to store true or false flags.
 */
final class BooleanField extends AbstractField
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
