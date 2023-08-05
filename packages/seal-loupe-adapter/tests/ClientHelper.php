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

namespace Schranz\Search\SEAL\Adapter\Loupe\Tests;

use Schranz\Search\SEAL\Adapter\AdapterFactory;
use Schranz\Search\SEAL\Adapter\Loupe\LoupeAdapterFactory;
use Schranz\Search\SEAL\Adapter\Loupe\LoupeHelper;

final class ClientHelper
{
    private static ?LoupeHelper $helper = null;

    public static function getHelper(): LoupeHelper
    {
        if (!self::$helper instanceof LoupeHelper) {
            $loupeAdapterFactory = new LoupeAdapterFactory();
            $factory = new AdapterFactory([
                'loupe' => $loupeAdapterFactory,
            ]);

            $parsedDsn = $factory->parseDsn(\trim((string) $_ENV['LOUPE_DSN']));
            self::$helper = $loupeAdapterFactory->createHelper($parsedDsn);
        }

        return self::$helper;
    }
}
