<?php

namespace Schranz\Search\SEAL\Adapter\RediSearch\Tests;

use Schranz\Search\SEAL\Adapter\RediSearch\RediSearchConnection;
use Schranz\Search\SEAL\Adapter\RediSearch\RediSearchSchemaManager;
use Schranz\Search\SEAL\Testing\AbstractConnectionTestCase;

class RediSearchConnectionTest extends AbstractConnectionTestCase
{
    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = ClientHelper::getClient();

        self::$connection = new RediSearchConnection(self::$client);
        self::$schemaManager = new RediSearchSchemaManager(self::$client);

        parent::setUpBeforeClass();
    }
    public function testFindMultipleIndexes(): void
    {
        $this->markTestSkipped('Not supported by RediSearch: https://github.com/schranz-search/schranz-search/issues/93');
    }

    public function testSaveDeleteIdentifierCondition(): void
    {
        $this->markTestSkipped('Not supported by RediSearch: https://github.com/schranz-search/schranz-search/issues/92');
    }
}
