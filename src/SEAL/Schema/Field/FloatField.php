<?php

namespace Schranz\Search\SEAL\Schema\Field;

use Schranz\Search\SEAL\Schema\FieldType;

/**
 * Type to store any PHP float value.
 */
final class FloatField extends AbstractField
{
    public function __construct(string $name, string $normalizedName)
    {
        parent::__construct($name, $normalizedName, FieldType::FLOAT);
    }
}
