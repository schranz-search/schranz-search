<?php

namespace Schranz\Search\SEAL\Adapter\Typesense\Tests;

use Schranz\Search\SEAL\Adapter\Typesense\TypesenseSchemaManager;
use Schranz\Search\SEAL\Testing\AbstractSchemaManagerTestCase;

class TypesenseSchemaManagerTest extends AbstractSchemaManagerTestCase
{
    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = ClientHelper::getClient();

        self::$schemaManager = new TypesenseSchemaManager(self::$client);
    }
}
