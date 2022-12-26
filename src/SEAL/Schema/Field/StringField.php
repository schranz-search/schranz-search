<?php

namespace Schranz\Search\SEAL\Schema\Field;

use Schranz\Search\SEAL\Schema\FieldType;

/**
 * Type to store any text, options can maybe use to specify it more specific.
 */
abstract class StringField extends AbstractField
{
    public function __construct(string $name, FieldType $type = FieldType::TEXT, bool $multiple = false)
    {
        parent::__construct($name, $type, $multiple);
    }
}
