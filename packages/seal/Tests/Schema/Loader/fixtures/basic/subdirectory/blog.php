<?php

declare(strict_types=1);

use Schranz\Search\SEAL\Schema\Field;
use Schranz\Search\SEAL\Schema\Index;

return new Index('blog', [
    'id' => new Field\IdentifierField('id'),
    'title' => new Field\TextField('title'),
    'description' => new Field\TextField('description'),
]);
