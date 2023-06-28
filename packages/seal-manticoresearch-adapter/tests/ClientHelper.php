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

namespace Schranz\Search\SEAL\Adapter\Manticoresearch\Tests;

use Manticoresearch\Client;

final class ClientHelper
{
    private static ?Client $client = null;

    public static function getClient(): Client
    {
        if (!self::$client instanceof \Manticoresearch\Client) {
            [$host, $port] = \explode(':', $_ENV['MANTICORESEARCH_HOST'] ?? '127.0.0.1:9308');

            self::$client = new Client([
                'host' => $host,
                'port' => $port,
            ]);
        }

        return self::$client;
    }
}
