<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Schema\Field;

/**
 * @readonly
 */
abstract class AbstractField
{
    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        public readonly string $name,
        public readonly bool $multiple,
        public readonly bool $searchable,
        public readonly bool $filterable,
        public readonly bool $sortable,
        public readonly array $options,
    ) {
    }
}
