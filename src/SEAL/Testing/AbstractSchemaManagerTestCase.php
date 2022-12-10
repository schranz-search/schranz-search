<?php

namespace Schranz\Search\SEAL\Testing;

use PHPUnit\Framework\TestCase;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;

abstract class AbstractSchemaManagerTest extends TestCase
{
    protected static SchemaManagerInterface $schemaManager;

    public function testIndex(): void
    {
        $schema = TestingHelper::createSchema();
        $index = $schema->indexes[TestingHelper::INDEX_SIMPLE];

        $this->assertFalse(static::$schemaManager->existIndex($index));

        static::$schemaManager->createIndex($index);

        $this->assertTrue(static::$schemaManager->existIndex($index));

        static::$schemaManager->dropIndex($index);

        $this->assertTrue(static::$schemaManager->existIndex($index));
    }
}
