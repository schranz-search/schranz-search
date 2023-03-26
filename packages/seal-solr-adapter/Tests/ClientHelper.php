<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\Solr\Tests;

use Solarium\Client;
use Solarium\Core\Client\Adapter\Curl;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class ClientHelper
{
    private static ?Client $client = null;

    public static function getClient(): Client
    {
        if (!self::$client instanceof \Solarium\Client) {
            [$host, $port] = \explode(':', $_ENV['SOLR_HOST'] ?? '127.0.0.1:8983');

            $adapter = new Curl();
            $eventDispatcher = new EventDispatcher();
            $options = [
                'endpoint' => [
                    'localhost' => [
                        'host' => $host,
                        'port' => $port,
                    ],
                ],
            ];

            self::$client = new Client($adapter, $eventDispatcher, $options);
        }

        return self::$client;
    }
}
