<?php

namespace Schranz\Search\SEAL\Testing;

use PHPUnit\Framework\TestCase;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Schema\Schema;

abstract class AbstractSchemaManagerTestCase extends TestCase
{
    protected static SchemaManagerInterface $schemaManager;

    protected Schema $schema;

    public function setUp(): void
    {
        $this->schema = TestingHelper::createSchema();
    }

    public function testSimpleSchema(): void
    {
        $index = $this->schema->indexes[TestingHelper::INDEX_SIMPLE];

        static::$schemaManager->dropIndex($index);

        $this->assertFalse(static::$schemaManager->existIndex($index));

        static::$schemaManager->createIndex($index);

        $this->waitForCreateIndex($index);

        $this->assertTrue(static::$schemaManager->existIndex($index));

        static::$schemaManager->dropIndex($index);

        $this->waitForDropIndex($index);

        $this->assertFalse(static::$schemaManager->existIndex($index));
    }

    public function testComplexSchema(): void
    {
        $index = $this->schema->indexes[TestingHelper::INDEX_COMPLEX];

        $this->assertFalse(static::$schemaManager->existIndex($index));

        static::$schemaManager->createIndex($index);

        $this->waitForCreateIndex();

        $this->assertTrue(static::$schemaManager->existIndex($index));

        static::$schemaManager->dropIndex($index);

        $this->waitForDropIndex();

        $this->assertFalse(static::$schemaManager->existIndex($index));
    }

    /**
     * For async adapters, we need to wait for the index to be created.
     */
    protected function waitForCreateIndex(): void
    {
    }

    /**
     * For async adapters, we need to wait for the index to be deleted.
     */
    protected function waitForDropIndex(): void
    {
    }
}
