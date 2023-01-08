<?php

namespace Schranz\Search\SEAL\Schema\Field;

/**
 * Type to store any text, options can maybe use to specify it more specific.
 */
final class TypedField extends AbstractField
{
    /**
     * @param array<string, array<string, AbstractField>> $types
     * @param array<string, mixed> $options
     */
    public function __construct(
        string $name,
        public readonly string $typeField,
        public readonly iterable $types,
        bool $multiple = false,
        array $options = []
    ) {
        $searchable = false;
        $filterable = false;
        $sortable = false;

        foreach ($types as $fields) {
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
