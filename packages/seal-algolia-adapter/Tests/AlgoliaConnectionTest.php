<?php

namespace Schranz\Search\SEAL\Adapter\Algolia\Tests;

use Schranz\Search\SEAL\Adapter\Algolia\AlgoliaConnection;
use Schranz\Search\SEAL\Adapter\Algolia\AlgoliaSchemaManager;
use Schranz\Search\SEAL\Testing\AbstractConnectionTestCase;

class AlgoliaConnectionTest extends AbstractConnectionTestCase
{
    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = ClientHelper::getClient();

        self::$connection = new AlgoliaConnection(self::$client);
        self::$schemaManager = new AlgoliaSchemaManager(self::$client);

        parent::setUpBeforeClass();
    }

    protected static function waitForCreateIndex(): void
    {
        usleep((int) ($_ENV['ALGOLIA_WAIT_TIME'] ?? 200_000));
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
