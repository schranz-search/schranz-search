<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Schema\Field;

/**
 * Type to store any text, options can maybe use to specify it more specific.
 */
final class TextField extends AbstractField
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
        array $options = [],
    ) {
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
