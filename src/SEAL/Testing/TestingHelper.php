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
            'created' => new Field\DateTimeField('created'),
            'commentsCount' => new Field\IntegerField('commentsCount'),
            'rating' => new Field\FloatField('rating'),
            'comments' => new Field\CollectionField('comments', new Field\ObjectField('', [
                'email' => new Field\TextField('email'),
                'text' => new Field\TextField('title'),
            ])),
            'tags' => new Field\CollectionField('comments', new Field\TextField('')),
            'categoryIds' => new Field\CollectionField('comments', new Field\IntegerField('')),
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
     *     created?: \DateTimeImmutable|null,
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
                'created' => new \DateTimeImmutable('2022-12-24 12:00:00'),
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
                'created' => new \DateTimeImmutable('2022-12-26 12:00:00'),
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
