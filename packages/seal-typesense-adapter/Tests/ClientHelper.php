<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\Typesense\Tests;

use Http\Discovery\HttpClientDiscovery;
use Typesense\Client;

final class ClientHelper
{
    private static ?Client $client = null;

    public static function getClient(): Client
    {
        if (!self::$client instanceof Client) {
            [$host, $port] = \explode(':', $_ENV['TYPESENSE_HOST'] ?? '127.0.0.1:8108');

            self::$client = new Client(
                [
                    'api_key' => $_ENV['TYPESENSE_API_KEY'],
                    'nodes' => [
                        [
                            'host' => $host,
                            'port' => $port,
                            'protocol' => 'http',
                        ],
                    ],
                    'client' => HttpClientDiscovery::find(),
                ],
            );
        }

        return self::$client;
    }
}
