<?php

declare(strict_types=1);

use Schranz\Search\SEAL\Schema\Field;
use Schranz\Search\SEAL\Schema\Index;

return new Index('news', [
    'id' => new Field\IdentifierField('id'),
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
        'text' => new Field\TextField('title'),
    ], multiple: true),
    'tags' => new Field\TextField('tags', multiple: true),
    'categoryIds' => new Field\IntegerField('categoryIds', multiple: true),
]);
