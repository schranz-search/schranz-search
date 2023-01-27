<?php

namespace Schranz\Search\SEAL\Adapter\Zinc\Tests;

use Schranz\Search\SEAL\Adapter\Zinc\ZincSchemaManager;
use Schranz\Search\SEAL\Testing\AbstractSchemaManagerTestCase;

class ZincSchemaManagerTest extends AbstractSchemaManagerTestCase
{
    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = ClientHelper::getClient();

        self::$schemaManager = new ZincSchemaManager(self::$client);
    }
}
