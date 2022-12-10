<?php

namespace Schranz\Search\SEAL\Schema;

final class Field
{
    /**
     * @param array<string, mixed> $typeOptions
     */
    public function __construct(
        public readonly string $name,
        public readonly string $normalizedName,
        public readonly string $type, // TODO enum?
        public readonly array $typeOptions = [], // TODO object?
    ) {}
}
