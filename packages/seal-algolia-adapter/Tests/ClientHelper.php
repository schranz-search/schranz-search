<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\Algolia\Tests;

use Algolia\AlgoliaSearch\SearchClient;

final class ClientHelper
{
    private static ?SearchClient $client = null;

    public static function getClient(): SearchClient
    {
        if (!self::$client instanceof SearchClient) {
            if (empty($_ENV['ALGOLIA_APPLICATION_ID']) || empty($_ENV['ALGOLIA_ADMIN_API_KEY'])) {
                throw new \InvalidArgumentException(
                    'The "ALGOLIA_APPLICATION_ID" and "ALGOLIA_ADMIN_API_KEY" environment variables need to be defined.',
                );
            }

            self::$client = SearchClient::create(
                \trim((string) $_ENV['ALGOLIA_APPLICATION_ID']),
                \trim((string) $_ENV['ALGOLIA_ADMIN_API_KEY']),
            );
        }

        return self::$client;
    }
}
