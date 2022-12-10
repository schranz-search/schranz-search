<?php

namespace Schranz\Search\SEAL\Schema\Field;

use Schranz\Search\SEAL\Schema\FieldType;

final class IdentifierField extends AbstractField
{
    public function __construct(string $name, string $normalizedName)
    {
        parent::__construct($name, $normalizedName, FieldType::IDENTIFIER);
    }
}
