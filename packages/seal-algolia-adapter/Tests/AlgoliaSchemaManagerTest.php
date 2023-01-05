<?php

namespace Schranz\Search\SEAL\Adapter\Algolia\Tests;

use Schranz\Search\SEAL\Adapter\Algolia\AlgoliaSchemaManager;
use Schranz\Search\SEAL\Testing\AbstractSchemaManagerTestCase;

class AlgoliaSchemaManagerTest extends AbstractSchemaManagerTestCase
{
    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = ClientHelper::getClient();

        self::$schemaManager = new AlgoliaSchemaManager(self::$client);
    }
}
