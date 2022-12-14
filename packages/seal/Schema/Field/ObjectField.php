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
    public function __construct(
        string $name,
        readonly public array $fields,
        bool $multiple = false,
        array $options = []
    ) {
        $searchable = false;
        $filterable = false;
        $sortable = false;

        foreach ($fields as $field) {
            if ($field->searchable) {
                $searchable = true;
            }

            if ($field->filterable) {
                $filterable = true;
            }

            if ($field->sortable) {
                $sortable = true;
            }
        }

        parent::__construct(
            $name,
            $multiple,
            $searchable,
            $filterable,
            $sortable,
            $options
        );
    }
}
