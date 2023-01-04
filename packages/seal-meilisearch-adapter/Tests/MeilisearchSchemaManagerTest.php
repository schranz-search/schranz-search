<?php

namespace Schranz\Search\SEAL\Adapter\Meilisearch\Tests;

use Schranz\Search\SEAL\Adapter\Meilisearch\MeilisearchSchemaManager;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Testing\AbstractSchemaManagerTestCase;
use Schranz\Search\SEAL\Testing\TestingHelper;

class MeilisearchSchemaManagerTest extends AbstractSchemaManagerTestCase
{
    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = ClientHelper::getClient();

        self::$schemaManager = new MeilisearchSchemaManager(self::$client);
    }

    protected static function waitForCreateIndex(): void
    {
        usleep((int) ($_ENV['MEILISEARCH_WAIT_TIME'] ?? 200_000));
    }

    protected static function waitForDropIndex(): void
    {
        usleep((int) ($_ENV['MEILISEARCH_WAIT_TIME'] ?? 200_000));
    }
}
