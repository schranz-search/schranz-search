<?php

namespace Schranz\Search\SEAL\Adapter\Typesense\Tests;

use Schranz\Search\SEAL\Adapter\Typesense\TypesenseSchemaManager;
use Schranz\Search\SEAL\Testing\AbstractSchemaManagerTestCase;

class TypesenseSchemaManagerTest extends AbstractSchemaManagerTestCase
{
    public static function setUpBeforeClass(): void
    {
        $client = ClientHelper::getClient();
        self::$schemaManager = new TypesenseSchemaManager($client);

        parent::setUpBeforeClass();
    }
}
