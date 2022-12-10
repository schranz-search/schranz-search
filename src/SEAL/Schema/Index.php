<?php

namespace Schranz\Search\SEAL\Schema;

use Schranz\Search\SEAL\Schema\Field\AbstractField;

final class Index
{
    /**
     * @param array<string, AbstractField> $fields
     */
    public function __construct(
        public readonly string $name,
        public readonly array $fields
    ) {}
}
