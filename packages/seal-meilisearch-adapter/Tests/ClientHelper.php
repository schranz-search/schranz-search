<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\Meilisearch\Tests;

use Meilisearch\Client;

final class ClientHelper
{
    private static ?Client $client = null;

    public static function getClient(): Client
    {
        if (!self::$client instanceof Client) {
            self::$client = new Client($_ENV['MEILISEARCH_HOST'] ?? '127.0.0.1:7700');
        }

        return self::$client;
    }
}
