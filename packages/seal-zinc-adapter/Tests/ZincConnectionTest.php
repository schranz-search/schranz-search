<?php

namespace Schranz\Search\SEAL\Adapter\Zinc\Tests;

use Schranz\Search\SEAL\Adapter\Zinc\ZincConnection;
use Schranz\Search\SEAL\Adapter\Zinc\ZincSchemaManager;
use Schranz\Search\SEAL\Testing\AbstractConnectionTestCase;

class ZincConnectionTest extends AbstractConnectionTestCase
{
    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = ClientHelper::getClient();

        self::$connection = new ZincConnection(self::$client);
        self::$schemaManager = new ZincSchemaManager(self::$client);

        parent::setUpBeforeClass();
    }

    public function testFindMultipleIndexes(): void
    {
        $this->markTestSkipped('Not supported by Zinc: https://github.com/schranz-search/schranz-search/issues/28');
    }
}
