<?php

namespace Schranz\Search\SEAL\Adapter\Zinc\Tests;

use Zinc\Client;

final class ClientHelper
{
    private static ?Client $client = null;

    public static function getClient(): Client
    {
        if (self::$client === null) {
            self::$client = new Client($_ENV['ZINC_HOST'] ?? '127.0.0.1:4080');
        }

        return self::$client;
    }
}
