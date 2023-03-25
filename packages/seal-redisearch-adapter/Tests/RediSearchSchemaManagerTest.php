<?php

namespace Schranz\Search\SEAL\Adapter\RediSearch\Tests;

use Schranz\Search\SEAL\Adapter\RediSearch\RediSearchSchemaManager;
use Schranz\Search\SEAL\Testing\AbstractSchemaManagerTestCase;

class RediSearchSchemaManagerTest extends AbstractSchemaManagerTestCase
{
    public static function setUpBeforeClass(): void
    {
        $client = ClientHelper::getClient();
        self::$schemaManager = new RediSearchSchemaManager($client);

        parent::setUpBeforeClass();
    }
}
