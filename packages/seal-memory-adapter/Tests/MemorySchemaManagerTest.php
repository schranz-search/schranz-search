<?php

namespace Schranz\Search\SEAL\Adapter\Memory\Tests;

use Schranz\Search\SEAL\Adapter\Memory\MemorySchemaManager;
use Schranz\Search\SEAL\Testing\AbstractSchemaManagerTestCase;

class MemorySchemaManagerTest extends AbstractSchemaManagerTestCase
{
    public static function setUpBeforeClass(): void
    {
        self::$schemaManager = new MemorySchemaManager();

        parent::setUpBeforeClass();
    }
}
