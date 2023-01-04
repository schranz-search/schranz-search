<?php

namespace Schranz\Search\SEAL\Adapter\Algolia\Tests;

use Schranz\Search\SEAL\Adapter\Algolia\AlgoliaAdapter;
use Schranz\Search\SEAL\Testing\AbstractAdapterTestCase;

class AlgoliaAdapterTest extends AbstractAdapterTestCase
{
    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = ClientHelper::getClient();

        self::$adapter = new AlgoliaAdapter(self::$client);
    }

    protected static function waitForCreateIndex(): void
    {
        usleep((int) ($_ENV['ALGOLIA_WAIT_TIME'] ?? 5_000_000));
    }

    protected static function waitForDropIndex(): void
    {
        usleep((int) ($_ENV['ALGOLIA_WAIT_TIME'] ?? 5_000_000));
    }

    public static function waitForAddDocuments(): void
    {
        usleep((int) ($_ENV['ALGOLIA_WAIT_TIME'] ?? 5_000_000));
    }

    public static function waitForDeleteDocuments(): void
    {
        usleep((int) ($_ENV['ALGOLIA_WAIT_TIME'] ?? 5_000_000));
    }
}
