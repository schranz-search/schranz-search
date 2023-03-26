<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Schema;

use Schranz\Search\SEAL\Schema\Exception\FieldByPathNotFoundException;
use Schranz\Search\SEAL\Schema\Field\AbstractField;
use Schranz\Search\SEAL\Schema\Field\IdentifierField;
use Schranz\Search\SEAL\Schema\Field\ObjectField;
use Schranz\Search\SEAL\Schema\Field\TypedField;

final class Index
{
    private ?IdentifierField $identifierField = null;

    /**
     * @var string[]
     */
    public readonly array $searchableFields;

    /**
     * @var string[]
     */
    public readonly array $sortableFields;

    /**
     * @var string[]
     */
    public readonly array $filterableFields;

    /**
     * @param array<string, AbstractField> $fields
     */
    public function __construct(
        public readonly string $name,
        public readonly array $fields,
    ) {
        $attributes = $this->getAttributes($fields);
        $this->searchableFields = $attributes['searchableFields'];
        $this->filterableFields = $attributes['filterableFields'];
        $this->sortableFields = $attributes['sortableFields'];
    }

    public function getIdentifierField(): IdentifierField
    {
        if (!$this->identifierField instanceof Field\IdentifierField) {
            $identifierField = null;
            foreach ($this->fields as $field) {
                if ($field instanceof IdentifierField) {
                    $identifierField = $field;
                    break;
                }
            }

            if (!$identifierField instanceof Field\IdentifierField) {
                throw new \LogicException(
                    'No "IdentifierField" found for index "' . $this->name . '"',
                );
            }

            $this->identifierField = $identifierField;
        }

        return $this->identifierField;
    }

    public function getFieldByPath(string $path): AbstractField
    {
        $pathParts = \explode('.', $path);
        $fields = $this->fields;

        while (true) {
            $field = $fields[\current($pathParts)] ?? null;

            if ($field instanceof TypedField) {
                $fields = $field->types[\current($pathParts)];
            } elseif ($field instanceof ObjectField) {
                $fields = $field->fields;
            } elseif ($field instanceof AbstractField) {
                return $field;
            } else {
                throw new FieldByPathNotFoundException($this->name, $path);
            }
        }
    }

    /**
     * @param Field\AbstractField[] $fields
     *
     * @return array{
     *     searchableFields: string[],
     *     filterableFields: string[],
     *     sortableFields: string[],
     * }
     */
    private function getAttributes(array $fields): array
    {
        $attributes = [
            'searchableFields' => [],
            'filterableFields' => [],
            'sortableFields' => [],
        ];

        foreach ($fields as $name => $field) {
            if ($field instanceof Field\ObjectField) {
                foreach ($this->getAttributes($field->fields) as $attributeType => $fieldNames) {
                    foreach ($fieldNames as $fieldName) {
                        $attributes[$attributeType][] = $name . '.' . $fieldName;
                    }
                }

                continue;
            } elseif ($field instanceof Field\TypedField) {
                foreach ($field->types as $type => $fields) {
                    foreach ($this->getAttributes($fields) as $attributeType => $fieldNames) {
                        foreach ($fieldNames as $fieldName) {
                            $attributes[$attributeType][] = $name . '.' . $type . '.' . $fieldName;
                        }
                    }
                }

                continue;
            }

            if ($field->searchable) {
                $attributes['searchableFields'][] = $name;
            }

            if ($field->filterable) {
                $attributes['filterableFields'][] = $name;
            }

            if ($field->sortable) {
                $attributes['sortableFields'][] = $name;
            }
        }

        return $attributes;
    }
}
