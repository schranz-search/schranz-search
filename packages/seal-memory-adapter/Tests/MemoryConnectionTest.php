<?php

namespace Schranz\Search\SEAL\Adapter\Memory\Tests;

use Schranz\Search\SEAL\Adapter\Memory\MemoryConnection;
use Schranz\Search\SEAL\Adapter\Memory\MemorySchemaManager;
use Schranz\Search\SEAL\Testing\AbstractConnectionTestCase;

class MemoryConnectionTest extends AbstractConnectionTestCase
{
    public static function setUpBeforeClass(): void
    {
        self::$connection = new MemoryConnection();
        self::$schemaManager = new MemorySchemaManager();

        parent::setUpBeforeClass();
    }
}
