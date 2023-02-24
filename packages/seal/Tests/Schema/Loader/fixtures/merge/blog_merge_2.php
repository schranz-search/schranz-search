<?php

use Schranz\Search\SEAL\Schema\Field;
use Schranz\Search\SEAL\Schema\Index;

return new Index('blog', [
    'description' => new Field\TextField('description', options: ['option2' => true]),
    'blocks' => new Field\TypedField('blocks', 'type', [
        'gallery' => [
            'media' => new Field\TextField('media', multiple: true),
        ],
    ], multiple: true),
    'footerText' => new Field\TextField('footerText'),
]);
