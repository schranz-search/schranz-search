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

    public static function setUpBeforeClass(): void
    {
        foreach (self::getSchema()->indexes as $index) {
            self::$schemaManager->createIndex($index);
        }
    }

    public static function tearDownAfterClass(): void
    {
        foreach (self::getSchema()->indexes as $index) {
            self::$schemaManager->dropIndex($index);
        }
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
            self::$connection->save($schema->indexes[TestingHelper::INDEX_COMPLEX], $document);
        }

        $this->waitForAddDocuments();

        $loadedDocuments = [];
        foreach ($documents as $document) {
            $search = new SearchBuilder($schema, self::$connection);
            $search->addIndex(TestingHelper::INDEX_COMPLEX);
            $search->addFilter(new IdentifierCondition($document['id']));

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
            self::$connection->delete($schema->indexes[TestingHelper::INDEX_COMPLEX], $document['id']);
        }

        $this->waitForDeleteDocuments();

        foreach ($documents as $document) {
            $search = new SearchBuilder($schema, self::$connection);
            $search->addIndex(TestingHelper::INDEX_COMPLEX);
            $search->addFilter(new IdentifierCondition($document['id']));

            $resultDocument = iterator_to_array($search->getResult(), false)[0] ?? null;

            $this->assertNull($resultDocument, 'Expected document with id "' . $document['id'] . '" to be deleted.');
        }
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
}
