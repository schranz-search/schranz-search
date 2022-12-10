<?php

namespace Schranz\Search\SEAL\Schema\Field;

use Schranz\Search\SEAL\Schema\FieldType;

/**
 * Type to store a list of objects.
 */
final class CollectionField extends AbstractField
{
    public function __construct(string $name, readonly public AbstractField $field)
    {
        parent::__construct($name, FieldType::COLLECTION);
    }
}
