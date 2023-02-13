<?php

namespace Schranz\Search\SEAL\Adapter\Typesense\Tests;

use Schranz\Search\SEAL\Adapter\Typesense\TypesenseConnection;
use Schranz\Search\SEAL\Adapter\Typesense\TypesenseSchemaManager;
use Schranz\Search\SEAL\Testing\AbstractConnectionTestCase;

class TypesenseConnectionTest extends AbstractConnectionTestCase
{
    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = ClientHelper::getClient();

        self::$connection = new TypesenseConnection(self::$client);
        self::$schemaManager = new TypesenseSchemaManager(self::$client);

        parent::setUpBeforeClass();
    }

    public function testFindMultipleIndexes(): void
    {
        $this->markTestSkipped('Not supported by Typesense: https://github.com/schranz-search/schranz-search/issues/98');
    }
}
