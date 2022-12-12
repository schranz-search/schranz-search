<?php

namespace Schranz\Search\SEAL\Schema\Field;

use Schranz\Search\SEAL\Schema\FieldType;

/**
 * Type to store fields inside a nested object.
 */
final class ObjectField extends AbstractField
{
    /**
     * @param array<string, AbstractField> $fields
     */
    public function __construct(string $name, readonly public array $fields, bool $multiple = false)
    {
        parent::__construct($name, FieldType::OBJECT, $multiple);
    }
}
