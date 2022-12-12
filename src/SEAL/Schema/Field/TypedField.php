<?php

namespace Schranz\Search\SEAL\Schema\Field;

use Schranz\Search\SEAL\Schema\FieldType;

/**
 * Type to store any text, options can maybe use to specify it more specific.
 */
final class TypedField extends AbstractField
{
    /**
     * @param array<string, array<string, AbstractField>> $types
     */
    public function __construct(
        string $name,
        public readonly string $typeField,
        public readonly iterable $types,
        bool $multiple = false,
    ) {
        parent::__construct($name, FieldType::TYPED, $multiple);
    }
}
