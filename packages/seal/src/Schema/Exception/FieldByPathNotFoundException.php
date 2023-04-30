<?php

declare(strict_types=1);

/*
 * This file is part of the Schranz Search package.
 *
 * (c) Alexander Schranz <alexander@sulu.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Schranz\Search\SEAL\Schema\Exception;

final class FieldByPathNotFoundException extends \Exception
{
    public function __construct(string $indexName, string $path, ?\Throwable $previous = null)
    {
        parent::__construct(
            'Field path "' . $path . '" not found in index "' . $indexName . '"',
            0,
            $previous,
        );
    }
}
