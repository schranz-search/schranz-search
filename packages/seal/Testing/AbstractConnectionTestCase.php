<?php

namespace Schranz\Search\SEAL\Testing;

use PHPUnit\Framework\TestCase;
use Schranz\Search\SEAL\Adapter\ConnectionInterface;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Schema\Schema;
use Schranz\Search\SEAL\Search\Condition\IdentifierCondition;
use Schranz\Search\SEAL\Search\SearchBuilder;

abstract class AbstractConnectionTestCase extends TestCase
{
    protected static ConnectionInterface $connection;

    protected static SchemaManagerInterface $schemaManager;

    protected static Schema $schema;

    private static TaskHelper $taskHelper;

    public function setUp(): void
    {
        self::$taskHelper = new TaskHelper();
    }

    public static function setUpBeforeClass(): void
    {
        self::$taskHelper = new TaskHelper();
        foreach (self::getSchema()->indexes as $index) {
            if (self::$schemaManager->existIndex($index)) {
                self::$schemaManager->dropIndex($index);
            }

            static::$taskHelper->tasks[] = self::$schemaManager->createIndex($index, ['return_slow_promise_result' => true]);
        }

        self::$taskHelper->waitForAll();
    }

    public static function tearDownAfterClass(): void
    {
        self::$taskHelper = new TaskHelper();
        foreach (self::getSchema()->indexes as $index) {
            self::$taskHelper->tasks[] = self::$schemaManager->dropIndex($index, ['return_slow_promise_result' => true]);
        }

        self::$taskHelper->waitForAll();
    }

    protected static function getSchema(): Schema
    {
        if (!isset(self::$schema)) {
            self::$schema = TestingHelper::createSchema();
        }

        return self::$schema;
    }

    public function testSaveDeleteIdentifierCondition(): void
    {
        $documents = TestingHelper::createComplexFixtures();

        $schema = self::getSchema();

        foreach ($documents as $document) {
            self::$taskHelper->tasks[] = self::$connection->save(
                $schema->indexes[TestingHelper::INDEX_COMPLEX],
                $document,
                ['return_slow_promise_result' => true]
            );
        }
        self::$taskHelper->waitForAll();

        $loadedDocuments = [];
        foreach ($documents as $document) {
            $search = new SearchBuilder($schema, self::$connection);
            $search->addIndex(TestingHelper::INDEX_COMPLEX);
            $search->addFilter(new IdentifierCondition($document['uuid']));

            $resultDocument = iterator_to_array($search->getResult(), false)[0] ?? null;

            if ($resultDocument) {
                $loadedDocuments[] = $resultDocument;
            }
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
            self::$taskHelper->tasks[] = self::$connection->delete(
                $schema->indexes[TestingHelper::INDEX_COMPLEX],
                $document['uuid'],
                ['return_slow_promise_result' => true]
            );
        }

        self::$taskHelper->waitForAll();

        foreach ($documents as $document) {
            $search = new SearchBuilder($schema, self::$connection);
            $search->addIndex(TestingHelper::INDEX_COMPLEX);
            $search->addFilter(new IdentifierCondition($document['uuid']));

            $resultDocument = iterator_to_array($search->getResult(), false)[0] ?? null;

            $this->assertNull($resultDocument, 'Expected document with uuid "' . $document['uuid'] . '" to be deleted.');
        }
    }

    public function testFindMultipleIndexes(): void
    {
        $document = TestingHelper::createSimpleFixtures()[0];

        $schema = self::getSchema();

        self::$connection->save(
            $schema->indexes[TestingHelper::INDEX_SIMPLE],
            $document,
            ['return_slow_promise_result' => true]
        )->wait();

        $search = new SearchBuilder($schema, self::$connection);
        $search->addIndex(TestingHelper::INDEX_COMPLEX);
        $search->addIndex(TestingHelper::INDEX_SIMPLE);
        $search->addFilter(new IdentifierCondition($document['id']));

        $expectedDocument = $document;
        $loadedDocument = iterator_to_array($search->getResult(), false)[0] ?? null;

        $this->assertSame($expectedDocument, $loadedDocument);

        self::$connection->delete(
            $schema->indexes[TestingHelper::INDEX_SIMPLE],
            $document['id'],
            ['return_slow_promise_result' => true]
        )->wait();
    }
}
