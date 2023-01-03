<?php

namespace Schranz\Search\SEAL\Schema\Field;

/**
 * Type to store any PHP float value.
 */
final class FloatField extends AbstractField
{
    /**
     * @param array<string, mixed> $options
     */
    public function __construct(string $name, bool $multiple = false, array $options = [])
    {
        parent::__construct($name, $multiple, $options);
    }
}
