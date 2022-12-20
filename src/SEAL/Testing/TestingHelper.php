<?php

namespace Schranz\Search\SEAL\Testing;

use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Schema\Schema;
use Schranz\Search\SEAL\Schema\Field;

class TestingHelper
{
    public const INDEX_COMPLEX = 'complex';

    public const INDEX_SIMPLE = 'simple';

    private function __construct() {}

    public static function createSchema(string $prefix = 'test_'): Schema
    {
        $complexFields = [
            'id' => new Field\IdentifierField('id'),
            'title' => new Field\TextField('title'),
            'title.raw' => new Field\TextField('title'),
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
            'created' => new Field\DateTimeField('created'),
            'commentsCount' => new Field\IntegerField('commentsCount'),
            'rating' => new Field\FloatField('rating'),
            'comments' => new Field\ObjectField('comments', [
                'email' => new Field\TextField('email'),
                'text' => new Field\TextField('title'),
            ], multiple: true),
            'tags' => new Field\TextField('comments', multiple: true),
            'categoryIds' => new Field\IntegerField('comments', multiple: true),
        ];

        $simpleFields = [
            'id' => new Field\IdentifierField('id'),
            'title' => new Field\TextField('title'),
        ];

        $complexIndex = new Index($prefix . 'news', $complexFields);
        $simpleIndex = new Index($prefix . 'blog', $simpleFields);

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
                'id' => '1',
                'title' => 'New Blog',
                'article' => '<article><h2>Some Subtitle</h2><p>A html field with some content</p></article>',
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
                'created' => '2022-12-24T12:00:00+01:00',
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
                'id' => '2',
                'title' => 'Other Blog',
                'article' => '<article><h2>Other title</h2><p>A html field with some content</p></article>',
                'created' => '2022-12-26T12:00:00+01:00',
                'commentsCount' => 0,
                'rating' => null,
                'comments' => [],
                'tags' => ['UI', 'UX'],
                'categoryIds' => [2, 3],
            ],
            [
                'id' => '3',
            ],
        ];
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
