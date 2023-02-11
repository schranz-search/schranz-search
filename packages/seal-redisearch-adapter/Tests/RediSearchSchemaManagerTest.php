<?php

namespace Schranz\Search\SEAL\Adapter\RediSearch\Tests;

use Schranz\Search\SEAL\Adapter\RediSearch\RediSearchSchemaManager;
use Schranz\Search\SEAL\Testing\AbstractSchemaManagerTestCase;

class RediSearchSchemaManagerTest extends AbstractSchemaManagerTestCase
{
    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = ClientHelper::getClient();

        self::$schemaManager = new RediSearchSchemaManager(self::$client);
    }
}
