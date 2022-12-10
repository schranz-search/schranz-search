# Schranz Search SEAL

**S**earch **E**ngine **A**bstraction **L**ayer with support to different search engines.

This package was highly inspired by [Doctrine DBAL](https://github.com/doctrine/dbal)
and [Flysystem](https://github.com/thephpleague/flysystem).

> This package is still in development and no adapters do exist yet.

## Usage

### Example Document

This should show the example document we want to create an index for:

```php
<?php

$document = [
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
];
```

> Currently, you can use some kind of normalizer like symfony/serializer to convert an object to an array
> and back to an object at current state a Document Mapper package does not yet exist. If provided in future
> it will be part of an own package which make usage of SEAL. Example like doctrine/orm using doctrine/dbal.

### Creating a Schema

The structure above we need to map into a schema.
A schema can contain multiple indexes. The following field types are available:

- `IDENTIFIER`: required unique identifier for the document
- `TEXT`: any text, options can maybe use to specify it more specific
- `BOOLEAN`: boolean to store true or false flags
- `FLOAT`: float to store any PHP float value
- `INTEGER`: integer to store any PHP int value
- `DATETIME`: datetime field to store date and date times
- `COLLECTION`: list of objects
- `OBJECT`: contains other fields nested in it

Currently, not keep in mind are types like geopoint, date, specific numeric types.
Specific text types like, url, path, ... should be specified over options in the future.

```php
<?php

use Schranz\Search\SEAL\Schema\Field;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Schema\Schema;

$fields = [
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

$prefix = 'test_'; // to avoid conflicts the indexes can be prefixed

$newsIndex = new Index($prefix . 'news', $fields);

$schema = new Schema([
    'news' => $newsIndex,
]);
```

The schema is serializable, so it can be stored in any cache and loaded fast.

### Create the engine

The engine requires an adapter and a previously created schema.

```php
<?php

use Schranz\Search\SEAL\Adapter\Elasticsearch\ElasticsearchAdapter; // does not yet exist
use Schranz\Search\SEAL\Engine;

$engine = new Engine(
    new ElasticsearchAdapter(/* ... */),
    $schema,
);
```

The engine is the main entry point to interact with the search engine:

#### Save a document

```php
$engine->saveDocument('news', $document);
```

> Will overwrite existing document if document with same id already exists.

#### Find a document

```php
$engine->getDocument('news', '1');
```

#### Delete a document

```php
$engine->deleteDocument('news', '1');
```

#### Search documents

```php
use Schranz\Search\SEAL\Search\Condition;

$documents = $engine->createSearchBuilder()
    ->addIndex('news')
    ->addFilter(new Condition\IdentifierCondition('1'))
    ->limit(1)
    ->offset(0)
    ->getResult();
```

> Condition is what in Elasticsearch are Query and Filters.

#### Create schema

```php
$engine->createSchema();
```

#### Drop schema

```php
$engine->dropSchema();
```

#### Create an index

```php
$engine->createIndex('news');
```

#### Drop an index

```php
$engine->dropIndex('news');
```

#### Check if index exists

```php
if (!$engine->existIndex('news')) {
    $engine->createIndex('news');
}
```
