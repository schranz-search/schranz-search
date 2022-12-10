<?php

namespace Schranz\Search\SEAL\Schema\Field;

use Schranz\Search\SEAL\Schema\FieldType;

/**
 * Type to store true or false flags.
 */
final class BooleanField extends AbstractField
{
    public function __construct(string $name)
    {
        parent::__construct($name, FieldType::BOOLEAN);
    }
}
