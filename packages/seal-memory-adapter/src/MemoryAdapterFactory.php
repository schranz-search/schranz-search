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

namespace Schranz\Search\SEAL\Adapter\Memory;

use Schranz\Search\SEAL\Adapter\AdapterFactoryInterface;
use Schranz\Search\SEAL\Adapter\AdapterInterface;

/**
 * @experimental
 */
class MemoryAdapterFactory implements AdapterFactoryInterface
{
    public function createAdapter(array $dsn): AdapterInterface
    {
        return new MemoryAdapter();
    }

    public static function getName(): string
    {
        return 'memory';
    }
}
