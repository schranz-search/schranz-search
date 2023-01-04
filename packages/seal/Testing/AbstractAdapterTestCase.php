<?php

namespace Schranz\Search\SEAL\Testing;

use PHPUnit\Framework\TestCase;
use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\Engine;
use Schranz\Search\SEAL\Exception\DocumentNotFoundException;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Schema\Schema;

abstract class AbstractAdapterTestCase extends TestCase
{
    protected static AdapterInterface $adapter;

    protected static Engine $engine;

    protected static Schema $schema;

    protected static function getEngine(): Engine
    {
        if (!isset(self::$engine)) {
            self::$schema = TestingHelper::createSchema();

            self::$engine = new Engine(
                self::$adapter,
                self::$schema,
            );
        }

        return self::$engine;
    }

    public function testIndex(): void
    {
        $engine = self::getEngine();
        $indexName = TestingHelper::INDEX_SIMPLE;

        $this->assertFalse($engine->existIndex($indexName));

        $engine->createIndex($indexName);

        $this->waitForCreateIndex();

        $this->assertTrue($engine->existIndex($indexName));

        $engine->dropIndex($indexName);

        $this->waitForDropIndex();

        $this->assertFalse($engine->existIndex($indexName));
    }

    public function testSchema(): void
    {
        $engine = self::getEngine();
        $indexes = self::$schema->indexes;

        $engine->createSchema();

        $this->waitForCreateIndex();

        foreach (array_keys($indexes) as $index) {
            $this->assertTrue($engine->existIndex($index));
        }

        $engine->dropSchema();

        $this->waitForDropIndex();

        foreach (array_keys($indexes) as $index) {
            $this->assertFalse($engine->existIndex($index));
        }
    }

    public function testDocument(): void
    {
        $engine = self::getEngine();
        $engine->createSchema();

        $documents = TestingHelper::createComplexFixtures();

        foreach ($documents as $document) {
            $engine->saveDocument(TestingHelper::INDEX_COMPLEX, $document);
        }

        $this->waitForAddDocuments();

        $loadedDocuments = [];
        foreach ($documents as $document) {
            $loadedDocuments[] = $engine->getDocument(TestingHelper::INDEX_COMPLEX, $document['id']);
        }

        $this->assertSame(
            count($documents),
            count($loadedDocuments),
        );

        foreach ($loadedDocuments as $key => $loadedDocument) {
            $expectedDocument = $documents[$key];

            $this->assertSame($expectedDocument, $loadedDocument);
        }

        foreach ($documents as $document) {
            $engine->deleteDocument(TestingHelper::INDEX_COMPLEX, $document['id']);
        }

        $this->waitForDeleteDocuments();

        foreach ($documents as $document) {
            $exceptionThrown = false;

            try {
                $engine->getDocument(TestingHelper::INDEX_COMPLEX, $document['id']);
            } catch (DocumentNotFoundException $e) {
                $exceptionThrown = true;
            }

            $this->assertTrue(
                $exceptionThrown,
                'Expected the exception "DocumentNotFoundException" to be thrown.'
            );
        }
    }

    public static function setUpBeforeClass(): void
    {
        self::getEngine()->dropSchema();
    }

    public static function tearDownAfterClass(): void
    {
        self::getEngine()->dropSchema();
    }

    /**
     * For async adapters, we need to wait for the index to add documents.
     */
    protected function waitForAddDocuments(): void
    {
    }

    /**
     * For async adapters, we need to wait for the index to delete documents.
     */
    protected function waitForDeleteDocuments(): void
    {
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
