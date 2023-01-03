<?php

namespace Schranz\Search\SEAL\Schema\Field;

/**
 * Type to store any text, options can maybe use to specify it more specific.
 */
final class TextField extends AbstractField
{
    public function __construct(string $name, bool $multiple = false, array $options = [])
    {
        parent::__construct($name, $multiple, $options);
    }
}
