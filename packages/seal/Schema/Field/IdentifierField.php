<?php

namespace Schranz\Search\SEAL\Schema\Field;

use Schranz\Search\SEAL\Schema\FieldType;

final class IdentifierField extends AbstractField
{
    public function __construct(string $name)
    {
        parent::__construct($name, FieldType::IDENTIFIER, false);
    }
}
