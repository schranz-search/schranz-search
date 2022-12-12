<?php

namespace Schranz\Search\SEAL\Schema;

enum FieldType
{
    /**
     * Type required unique identifier for the document stored as string.
     */
    case IDENTIFIER;

    /**
     * Type to store any text, options can maybe use to specify it more specific.
     */
    case TEXT;

    /**
     * Type to store true or false flags.
     */
    case BOOLEAN;

    /**
     * Type to store any PHP float value.
     */
    case FLOAT;

    /**
     * Type to store any PHP int value.
     */
    case INTEGER;

    /**
     * Type to store date and date times.
     */
    case DATETIME;

    /**
     * Type to store fields inside a nested object.
     */
    case OBJECT;

    /**
     * Type to store fields different based on a type field.
     */
    case TYPED;
}
