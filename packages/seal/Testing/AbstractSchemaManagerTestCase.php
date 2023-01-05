<?php

namespace Schranz\Search\SEAL\Testing;

use PHPUnit\Framework\TestCase;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
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

        $this->assertFalse(static::$schemaManager->existIndex($index));

        $task = static::$schemaManager->createIndex($index, ['return_slow_promise_result' => true]);
        $task->wait();
        static::waitForCreateIndex(); // TODO remove when all adapter migrated to $task->wait();

        $this->assertTrue(static::$schemaManager->existIndex($index));

        $task = static::$schemaManager->dropIndex($index, ['return_slow_promise_result' => true]);
        $task->wait();
        static::waitForDropIndex(); // TODO remove when all adapter migrated to $task->wait();

        $this->assertFalse(static::$schemaManager->existIndex($index));
    }

    public function testComplexSchema(): void
    {
        $index = $this->schema->indexes[TestingHelper::INDEX_COMPLEX];

        $this->assertFalse(static::$schemaManager->existIndex($index));

        $task = static::$schemaManager->createIndex($index, ['return_slow_promise_result' => true]);
        $task->wait();
        static::waitForCreateIndex(); // TODO remove when all adapter migrated to $task->wait();

        $this->assertTrue(static::$schemaManager->existIndex($index));

        $task = static::$schemaManager->dropIndex($index, ['return_slow_promise_result' => true]);
        $task->wait();
        static::waitForDropIndex(); // TODO remove when all adapter migrated to $task->wait();

        $this->assertFalse(static::$schemaManager->existIndex($index));
    }

    /**
     * @deprecated Use return AsyncTask instead.
     *
     * For async adapters, we need to wait for the index to be created.
     */
    protected static function waitForCreateIndex(): void
    {
        // TODO remove when all adapter migrated to $task->wait();
    }

    /**
     * @deprecated Use return AsyncTask instead.
     *
     * For async adapters, we need to wait for the index to be deleted.
     */
    protected static function waitForDropIndex(): void
    {
        // TODO remove when all adapter migrated to $task->wait();
    }
}
