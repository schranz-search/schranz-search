<?php

namespace Schranz\Search\SEAL\Schema\Field;

/**
 * Type to store the identifier this field type only exist once per index.
 */
final class IdentifierField extends AbstractField
{
    public function __construct(string $name)
    {
        parent::__construct(
            $name,
            multiple: false,
            searchable: false,
            filterable: true,
            sortable: true,
            options: [],
        );
    }
}
