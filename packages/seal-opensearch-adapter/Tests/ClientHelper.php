<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\Opensearch\Tests;

use OpenSearch\Client;
use OpenSearch\ClientBuilder;

final class ClientHelper
{
    private static ?Client $client = null;

    public static function getClient(): Client
    {
        if (!self::$client instanceof Client) {
            self::$client = ClientBuilder::create()->setHosts([
                $_ENV['OPENSEARCH_HOST'] ?? '127.0.0.1:9200',
            ])->build();
        }

        return self::$client;
    }
}
