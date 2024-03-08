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

namespace Schranz\Search\SEAL\Adapter\Algolia\Tests;

use Algolia\AlgoliaSearch\SearchClient;
use Schranz\Search\SEAL\Adapter\AdapterFactory;
use Schranz\Search\SEAL\Adapter\Algolia\AlgoliaAdapterFactory;

final class ClientHelper
{
    private static SearchClient|null $client = null;

    public static function getClient(): SearchClient
    {
        if (!self::$client instanceof SearchClient) {
            if (!empty($_ENV['ALGOLIA_DSN'])) {
                $algoliaAdapterFactory = new AlgoliaAdapterFactory();
                $factory = new AdapterFactory([
                    'algolia' => $algoliaAdapterFactory,
                ]);

                $parsedDsn = $factory->parseDsn(\trim((string) $_ENV['ALGOLIA_DSN']));
                self::$client = $algoliaAdapterFactory->createClient($parsedDsn);
            } elseif (empty($_ENV['ALGOLIA_APPLICATION_ID']) || empty($_ENV['ALGOLIA_ADMIN_API_KEY'])) {
                throw new \InvalidArgumentException(
                    'The "ALGOLIA_APPLICATION_ID" and "ALGOLIA_ADMIN_API_KEY" environment variables need to be defined.',
                );
            }

            self::$client ??= SearchClient::create(
                \trim((string) $_ENV['ALGOLIA_APPLICATION_ID']),
                \trim((string) $_ENV['ALGOLIA_ADMIN_API_KEY']),
            );
        }

        return self::$client;
    }
}
