<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\RediSearch\Tests;

final class ClientHelper
{
    private static ?\Redis $client = null;

    public static function getClient(): \Redis
    {
        if (!self::$client instanceof \Redis) {
            [$host, $port] = \explode(':', $_ENV['REDIS_HOST'] ?? '127.0.0.1:6379');

            self::$client = new \Redis();
            self::$client->pconnect($host, (int) $port);
            self::$client->auth($_ENV['REDIS_PASSWORD'] ?? 'supersecure');
        }

        return self::$client;
    }
}
