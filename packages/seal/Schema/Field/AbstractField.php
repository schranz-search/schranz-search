<?php

namespace Schranz\Search\SEAL\Schema\Field;

use Schranz\Search\SEAL\Schema\FieldType;

abstract class AbstractField
{
    public function __construct(
        public readonly string $name,
        public readonly FieldType $type,
        public readonly bool $multiple,
    ) {}
}
