<?php

namespace Schranz\Search\SEAL\Testing;

use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Schema\Schema;
use Schranz\Search\SEAL\Schema\Field;
use Schranz\Search\SEAL\Task\TaskInterface;

class TestingHelper
{
    public const INDEX_COMPLEX = 'complex';

    public const INDEX_SIMPLE = 'simple';

    private function __construct() {}

    public static function createSchema(): Schema
    {
        $prefix = getenv('TEST_INDEX_PREFIX') ?: $_ENV['TEST_INDEX_PREFIX'] ?? 'test_';

        $complexFields = [
            'uuid' => new Field\IdentifierField('uuid'),
            'title' => new Field\TextField('title'),
            'header' => new Field\TypedField('header', 'type', [
                'image' => [
                    'media' => new Field\IntegerField('media'),
                ],
                'video' => [
                    'media' => new Field\TextField('media'),
                ],
            ]),
            'article' => new Field\TextField('article'),
            'blocks' => new Field\TypedField('blocks', 'type', [
                'text' => [
                    'title' => new Field\TextField('title'),
                    'description' => new Field\TextField('description'),
                    'media' => new Field\IntegerField('media', multiple: true),
                ],
                'embed' => [
                    'title' => new Field\TextField('title'),
                    'media' => new Field\TextField('media'),
                ],
            ], multiple: true),
            'footer' => new Field\ObjectField('footer', [
                'title' => new Field\TextField('title'),
            ]),
            'created' => new Field\DateTimeField('created'),
            'commentsCount' => new Field\IntegerField('commentsCount'),
            'rating' => new Field\FloatField('rating'),
            'comments' => new Field\ObjectField('comments', [
                'email' => new Field\TextField('email'),
                'text' => new Field\TextField('text'),
            ], multiple: true),
            'tags' => new Field\TextField('tags', multiple: true),
            'categoryIds' => new Field\IntegerField('categoryIds', multiple: true),
        ];

        $simpleFields = [
            'id' => new Field\IdentifierField('id'),
            'title' => new Field\TextField('title'),
        ];

        $complexIndex = new Index($prefix . 'complex', $complexFields);
        $simpleIndex = new Index($prefix . 'simple', $simpleFields);

        return new Schema([
            self::INDEX_COMPLEX => $complexIndex,
            self::INDEX_SIMPLE => $simpleIndex,
        ]);
    }

    /**
     * @return array<array{
     *     id: string,
     *     title?: string|null,
     *     article?: string|null,
     *     blocks?: array<array{
     *          type: string,
     *          title?: string|null,
     *          description?: string|null
     *          media?: int[]|string,
     *     }>,
     *     created?: \string|null,
     *     commentsCount?: int|null,
     *     rating?: int|null,
     *     comments?: array<array{
     *         email?: string|null,
     *         text?: string|null,
     *     }>|null,
     *     tags?: string[]|null,
     *     categoryIds?: string[]|null,
     * }>
     */
    public static function createComplexFixtures(): array
    {
        return [
            [
                'uuid' => '23b30f01-d8fd-4dca-b36a-4710e360a965',
                'title' => 'New Blog',
                'header' => [
                    'type' => 'image',
                    'media' => 1,
                ],
                'article' => '<article><h2>New Subtitle</h2><p>A html field with some content</p></article>',
                'blocks' => [
                    [
                        'type' => 'text',
                        'title' => 'Titel',
                        'description' => '<p>Description</p>',
                        'media' => [3, 4],
                    ],
                    [
                        'type' => 'text',
                        'title' => 'Titel 2',
                        'description' => '<p>Description 2</p>',
                    ],
                    [
                        'type' => 'embed',
                        'title' => 'Video',
                        'media' => 'https://www.youtube.com/watch?v=iYM2zFP3Zn0',
                    ],
                ],
                'footer' => [
                    'title' => 'New Footer',
                ],
                'created' => '2022-01-24T12:00:00+01:00',
                'commentsCount' => 2,
                'rating' => 3.5,
                'comments' => [
                    [
                        'email' => 'admin@localhost',
                        'text' => 'Awesome blog!',
                    ],
                    [
                        'email' => 'example@localhost',
                        'text' => 'Like this blog!',
                    ],
                ],
                'tags' => ['Tech', 'UI'],
                'categoryIds' => [1, 2],
            ],
            [
                'uuid' => '79848403-c1a1-4420-bcc2-06ed537e0d4d',
                'title' => 'Other Blog',
                'header' => [
                    'type' => 'video',
                    'media' => 'https://www.youtube.com/watch?v=iYM2zFP3Zn0',
                ],
                'article' => '<article><h2>Other Subtitle</h2><p>A html field with some content</p></article>',
                'footer' => [
                    'title' => 'Other Footer',
                ],
                'created' => '2022-12-26T12:00:00+01:00',
                'commentsCount' => 0,
                'rating' => 2.5,
                'comments' => [],
                'tags' => ['UI', 'UX'],
                'categoryIds' => [2, 3],
            ],
            [
                'uuid' => '8d90e7d9-2b56-4980-90ce-f91d020cee53',
                'title' => 'Other Thing',
                'article' => '<article><h2>Other Thing</h2><p>A html field with some content</p></article>',
                'footer' => [
                    'title' => 'Other Footer',
                ],
                'created' => '2023-02-03T12:00:00+01:00',
                'commentsCount' => 0,
                'rating' => null,
                'comments' => [],
                'tags' => ['Tech'],
                'categoryIds' => [3, 4],
            ],
            [
                'uuid' => '97cd3e94-c17f-4c11-a22b-d9da2e5318cd',
            ],
        ];
    }

    /**
     * @param \Closure(TaskInterface[]: $tasks) $callback
     */
    public static function waitForTasks(\Closure $callback): void
    {
        /** @var TaskInterface[] $tasks */
        $tasks = [];

        ($callback)($tasks);

        foreach ($tasks as $task) {
            $task->wait();
        }
    }

    /**
     * @return array<array{
     *     id: string,
     *     title?: string|null,
     * }>
     */
    public static function createSimpleFixtures(): array
    {
        return [
            [
                'id' => '1',
                'title' => 'Simple Title',
            ],
            [
                'id' => '2',
                'title' => 'Other Title',
            ],
            [
                'id' => '3',
            ],
        ];
    }
}
