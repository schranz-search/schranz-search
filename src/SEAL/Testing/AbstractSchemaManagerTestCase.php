<?php

namespace Schranz\Search\SEAL\Testing;

use PHPUnit\Framework\TestCase;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;

class AbstractSchemaManagerTest extends TestCase
{
    protected SchemaManagerInterface $schemaManager;

    public function testIndex(): void
    {
        $schema = TestingHelper::createSchema();
        $index = $schema->indexes[TestingHelper::INDEX_SIMPLE];

        $this->assertFalse($this->schemaManager->existIndex($index));

        $this->schemaManager->createIndex($index);

        $this->assertTrue($this->schemaManager->existIndex($index));

        $this->schemaManager->dropIndex($index);

        $this->assertTrue($this->schemaManager->existIndex($index));
    }
}
