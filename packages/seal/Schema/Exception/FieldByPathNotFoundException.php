<?php

namespace Schranz\Search\SEAL\Schema\Exception;

final class FieldByPathNotFoundException extends \Exception
{
    public function __construct(string $indexName, string $path, ?\Throwable $previous = null)
    {
        parent::__construct(
            'Field path "' . $path . '" not found in index "' . $indexName . '"',
            0,
            $previous
        );
    }
}
