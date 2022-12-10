<?php

namespace Schranz\Search\SEAL\Schema\Field;

use Schranz\Search\SEAL\Schema\FieldType;

/**
 * Type to store any text, options can maybe use to specify it more specific.
 */
final class TextField extends AbstractField
{
    public function __construct(string $name, string $normalizedName)
    {
        parent::__construct($name, $normalizedName, FieldType::TEXT);
    }
}
