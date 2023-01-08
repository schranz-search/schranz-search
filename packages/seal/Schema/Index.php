<?php

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
     * @param array<string, AbstractField> $fields
     */
    public function __construct(
        public readonly string $name,
        public readonly array $fields
    ) {}

    public function getIdentifierField(): IdentifierField
    {
        if ($this->identifierField === null) {
            $identifierField = null;
            foreach ($this->fields as $field) {
                if ($field instanceof IdentifierField) {
                    $identifierField = $field;
                    break;
                }
            }

            if ($identifierField === null) {
                throw new \LogicException(
                    'No "IdentifierField" found for index "' . $this->name . '"'
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

        do {
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
        } while (true);
    }
}
