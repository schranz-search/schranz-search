<?php

namespace Schranz\Search\SEAL\Schema\Field;

/**
 * Type to store fields inside a nested object.
 */
final class ObjectField extends AbstractField
{
    /**
     * @param array<string, AbstractField> $fields
     * @param array<string, mixed> $options
     */
    public function __construct(string $name, readonly public array $fields, bool $multiple = false, array $options = [])
    {
        parent::__construct($name, $multiple, $options);
    }
}
