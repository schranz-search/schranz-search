<?php

namespace Schranz\Search\SEAL\Schema\Field;

abstract class AbstractField
{
    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        public readonly string $name,
        public readonly bool $multiple,
        public readonly array $options,
    ) {}
}
