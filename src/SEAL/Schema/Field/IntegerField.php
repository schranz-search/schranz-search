<?php

namespace Schranz\Search\SEAL\Schema\Field;

use Schranz\Search\SEAL\Schema\FieldType;

/**
 * Type to store any PHP int value.
 */
final class IntegerField extends AbstractField
{
    public function __construct(string $name, bool $multiple = false)
    {
        parent::__construct($name, FieldType::INTEGER, $multiple);
    }
}
