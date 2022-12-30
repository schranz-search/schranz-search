<?php

namespace Schranz\Search\SEAL\Adapter\Elasticsearch\Tests;

use Schranz\Search\SEAL\Adapter\Elasticsearch\ElasticsearchSchemaManager;
use Schranz\Search\SEAL\Testing\AbstractSchemaManagerTestCase;
use Schranz\Search\SEAL\Testing\TestingHelper;

class ElasticsearchSchemaManagerTest extends AbstractSchemaManagerTestCase
{
    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = ClientHelper::getClient();

        self::$schemaManager = new ElasticsearchSchemaManager(self::$client);
    }

    public function testSimpleElasticsearchMapping(): void
    {
        $index = $this->schema->indexes[TestingHelper::INDEX_SIMPLE];
        static::$schemaManager->createIndex($index);

        $mapping = self::$client->indices()->getMapping([
            'index' => $index->name,
        ])->asArray();

        $this->assertTrue(isset($mapping['test_simple']['mappings']['properties']));

        $this->assertSame([
            'id' => [
                'type' => 'keyword',
            ],
            'title' => [
                'type' => 'text',
            ],
        ], $mapping['test_simple']['mappings']['properties']);

        static::$schemaManager->dropIndex($index);
    }

    public function testComplexElasticsearchMapping(): void
    {
        $index = $this->schema->indexes[TestingHelper::INDEX_COMPLEX];
        static::$schemaManager->createIndex($index);

        $mapping = self::$client->indices()->getMapping([
            'index' => $index->name,
        ])->asArray();

        $this->assertTrue(isset($mapping['test_complex']['mappings']['properties']));

        $this->assertSame([
            'article' => [
                'type' => 'text',
            ],
            'blocks' => [
                'properties' => [
                    'embed' => [
                        'type' => 'nested',
                        'properties' => [
                            'media' => [
                                'type' => 'text',
                            ],
                            'title' => [
                                'type' => 'text',
                            ],
                        ],
                    ],
                    'text' => [
                        'type' => 'nested',
                        'properties' => [
                            'description' => [
                                'type' => 'text',
                            ],
                            'media' => [
                                'type' => 'integer',
                            ],
                            'title' => [
                                'type' => 'text',
                            ],
                        ],
                    ],
                ],
            ],
            'categoryIds' => [
                'type' => 'integer',
            ],
            'comments' => [
                'properties' => [
                    'email' => [
                        'type' => 'text',
                    ],
                    'text' => [
                        'type' => 'text',
                    ],
                ],
            ],
            'commentsCount' => [
                'type' => 'integer',
            ],
            'created' => [
                'type' => 'date',
                'format' => 'YYYY-MM-DD\'T\'HH:mm:ssZ',
            ],
            'id' => [
                'type' => 'keyword',
            ],
            'rating' => [
                'type' => 'float',
            ],
            'tags' => [
                'type' => 'text',
            ],
            'title' => [
                'type' => 'text',
            ],
        ], $mapping['test_complex']['mappings']['properties']);

        static::$schemaManager->dropIndex($index);
    }
}
