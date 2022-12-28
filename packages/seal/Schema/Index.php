<?php

namespace Schranz\Search\SEAL\Schema;

use Schranz\Search\SEAL\Schema\Field\AbstractField;
use Schranz\Search\SEAL\Schema\Field\IdentifierField;

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
}
