<?php

namespace Schranz\Search\SEAL\Adapter\Opensearch\Tests;

use Schranz\Search\SEAL\Adapter\Opensearch\OpensearchSchemaManager;
use Schranz\Search\SEAL\Testing\AbstractSchemaManagerTestCase;
use Schranz\Search\SEAL\Testing\TestingHelper;

class OpensearchSchemaManagerTest extends AbstractSchemaManagerTestCase
{
    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = ClientHelper::getClient();

        self::$schemaManager = new OpensearchSchemaManager(self::$client);
    }

    public function testSimpleOpensearchMapping(): void
    {
        $index = $this->schema->indexes[TestingHelper::INDEX_SIMPLE];
        $task = static::$schemaManager->createIndex($index, ['return_slow_promise_result' => true]);
        $task->wait();

        $mapping = self::$client->indices()->getMapping([
            'index' => $index->name,
        ]);

        $task = static::$schemaManager->dropIndex($index, ['return_slow_promise_result' => true]);
        $task->wait();

        $this->assertTrue(isset($mapping[$index->name]['mappings']['properties']));

        $this->assertSame([
            'id' => [
                'type' => 'keyword',
            ],
            'title' => [
                'type' => 'text',
            ],
        ], $mapping[$index->name]['mappings']['properties']);
    }

    public function testComplexOpensearchMapping(): void
    {
        $index = $this->schema->indexes[TestingHelper::INDEX_COMPLEX];
        $task = static::$schemaManager->createIndex($index, ['return_slow_promise_result' => true]);
        $task->wait();

        $mapping = self::$client->indices()->getMapping([
            'index' => $index->name,
        ]);

        $task = static::$schemaManager->dropIndex($index, ['return_slow_promise_result' => true]);
        $task->wait();

        $this->assertTrue(isset($mapping[$index->name]['mappings']['properties']));

        $this->assertSame([
            'article' => [
                'type' => 'text',
            ],
            'blocks' => [
                'properties' => [
                    'embed' => [
                        'properties' => [
                            '_originalIndex' => [
                                'type' => 'integer',
                                'index' => false,
                            ],
                            'media' => [
                                'type' => 'text',
                                'index' => false,
                            ],
                            'title' => [
                                'type' => 'text',
                            ],
                        ],
                    ],
                    'text' => [
                        'properties' => [
                            '_originalIndex' => [
                                'type' => 'integer',
                                'index' => false,
                            ],
                            'description' => [
                                'type' => 'text',
                            ],
                            'media' => [
                                'type' => 'integer',
                                'index' => false,
                                'doc_values' => false
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
                        'index' => false,
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
            ],
            'footer' => [
                'properties' => [
                    'title' => [
                        'type' => 'text',
                    ],
                ],
            ],
            'header' => [
                'properties' => [
                    'image' => [
                        'properties' => [
                            'media' => [
                                'type' => 'integer',
                                'index' => false,
                                'doc_values' => false,
                            ],
                        ],
                    ],
                    'video' => [
                        'properties' => [
                            'media' => [
                                'type' => 'text',
                                'index' => false,
                            ],
                        ],
                    ],
                ],
            ],
            'rating' => [
                'type' => 'float',
            ],
            'tags' => [
                'type' => 'text',
                'fields' => [
                    'raw' => [
                        'type' => 'keyword',
                    ],
                ],
            ],
            'title' => [
                'type' => 'text',
            ],
            'uuid' => [
                'type' => 'keyword',
            ],
        ], $mapping[$index->name]['mappings']['properties']);
    }
}
