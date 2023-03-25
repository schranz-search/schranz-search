<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Testing;

use PHPUnit\Framework\TestCase;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Schema\Schema;

abstract class AbstractSchemaManagerTestCase extends TestCase
{
    protected static SchemaManagerInterface $schemaManager;

    protected Schema $schema;

    protected function setUp(): void
    {
        $this->schema = TestingHelper::createSchema();
    }

    public function testSimpleSchema(): void
    {
        $index = $this->schema->indexes[TestingHelper::INDEX_SIMPLE];

        $this->assertFalse(static::$schemaManager->existIndex($index));

        $task = static::$schemaManager->createIndex($index, ['return_slow_promise_result' => true]);
        $task->wait();

        $this->assertTrue(static::$schemaManager->existIndex($index));

        $task = static::$schemaManager->dropIndex($index, ['return_slow_promise_result' => true]);
        $task->wait();

        $this->assertFalse(static::$schemaManager->existIndex($index));
    }

    public function testComplexSchema(): void
    {
        $index = $this->schema->indexes[TestingHelper::INDEX_COMPLEX];

        $this->assertFalse(static::$schemaManager->existIndex($index));

        $task = static::$schemaManager->createIndex($index, ['return_slow_promise_result' => true]);
        $task->wait();

        $this->assertTrue(static::$schemaManager->existIndex($index));

        $task = static::$schemaManager->dropIndex($index, ['return_slow_promise_result' => true]);
        $task->wait();

        $this->assertFalse(static::$schemaManager->existIndex($index));
    }
}
