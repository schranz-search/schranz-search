<?php

namespace Schranz\Search\SEAL\Testing;

use PHPUnit\Framework\TestCase;
use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\Engine;
use Schranz\Search\SEAL\Exception\DocumentNotFoundException;

class AbstractAdapterTestCase extends TestCase
{
    protected AdapterInterface $adapter;

    public function testIndex(): void
    {
        $schema = TestingHelper::createSchema('index_');

        $engine = new Engine(
            $this->adapter,
            $schema
        );

        $index = $schema->indexes[TestingHelper::INDEX_SIMPLE];

        $this->assertFalse($engine->existIndex($index));

        $engine->createIndex($index);

        $this->assertTrue($engine->existIndex($index));

        $engine->dropIndex($index);

        $this->assertTrue($engine->existIndex($index));
    }

    public function testSchema(): void
    {
        $schema = TestingHelper::createSchema('schema_');

        $engine = new Engine(
            $this->adapter,
            $schema
        );

        $engine->createSchema();

        foreach (array_keys($schema->indexes) as $index) {
            $this->assertTrue($engine->existIndex($index));
        }

        $engine->dropSchema();

        foreach (array_keys($schema->indexes) as $index) {
            $this->assertFalse($engine->existIndex($index));
        }
    }

    public function testDocument(): void
    {
        $schema = TestingHelper::createSchema();

        $engine = new Engine(
            $this->adapter,
            $schema
        );

        $documents = TestingHelper::createComplexFixtures();

        foreach ($documents as $document) {
            $engine->indexDocument(TestingHelper::INDEX_COMPLEX, $document);
        }

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

            $this->assertSame(
                $expectedDocument['created']?->format('c') ?? null,
                $loadedDocument['created']?->format('c') ?? null,
            );

            unset($loadedDocument['created']);
            unset($expectedDocument['created']);

            $this->assertSame($expectedDocument, $loadedDocument);
        }

        foreach ($documents as $document) {
            $engine->deleteDocument(TestingHelper::INDEX_COMPLEX, $document['id']);
        }

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
}
