<?php

namespace Schranz\Search\SEAL\Adapter\Typesense\Tests;

use Http\Client\Curl\Client as CurlClient;
use Http\Discovery\Psr17FactoryDiscovery;
use Typesense\Client;

final class ClientHelper
{
    private static ?Client $client = null;

    public static function getClient(): Client
    {
        if (self::$client === null) {
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
                    'client' => new CurlClient(Psr17FactoryDiscovery::findResponseFactory(), Psr17FactoryDiscovery::findStreamFactory()),
                ]
            );
        }

        return self::$client;
    }
}
