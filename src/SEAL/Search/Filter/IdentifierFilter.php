<?php

namespace Schranz\Search\SEAL\Search\Filter;

class IdentifierFilter
{
    public function __construct(
        readonly string $identifier,
    ) {
    }
}
