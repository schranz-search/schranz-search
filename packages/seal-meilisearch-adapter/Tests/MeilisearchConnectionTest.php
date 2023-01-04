<?php

namespace Schranz\Search\SEAL\Adapter\Meilisearch\Tests;

use Schranz\Search\SEAL\Adapter\Meilisearch\MeilisearchConnection;
use Schranz\Search\SEAL\Adapter\Meilisearch\MeilisearchSchemaManager;
use Schranz\Search\SEAL\Testing\AbstractConnectionTestCase;

class MeilisearchConnectionTest extends AbstractConnectionTestCase
{
    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = ClientHelper::getClient();

        self::$connection = new MeilisearchConnection(self::$client);
        self::$schemaManager = new MeilisearchSchemaManager(self::$client);

        parent::setUpBeforeClass();
    }

    protected static function waitForCreateIndex(): void
    {
        usleep((int) ($_ENV['MEILISEARCH_WAIT_TIME'] ?? 100_000));
    }

    protected static function waitForDropIndex(): void
    {
        usleep((int) ($_ENV['MEILISEARCH_WAIT_TIME'] ?? 100_000));
    }

    public static function waitForAddDocuments(): void
    {
        usleep((int) ($_ENV['MEILISEARCH_WAIT_TIME'] ?? 100_000));
    }

    public static function waitForDeleteDocuments(): void
    {
        usleep((int) ($_ENV['MEILISEARCH_WAIT_TIME'] ?? 100_000));
    }
}
