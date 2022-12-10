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
    public function __construct(string $name, string $normalizedName, readonly public array $fields)
    {
        parent::__construct($name, $normalizedName, FieldType::OBJECT);
    }
}
