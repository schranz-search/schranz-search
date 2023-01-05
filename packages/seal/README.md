<div align="center">
    <img alt="Schranz Search Logo with a Seal on it with a magnifying glass" src="https://avatars.githubusercontent.com/u/120221538?s=400&v=5" width="200" height="200">
</div>

<h1 align="center">Schranz Search SEAL</h1>

<div align="center">

**S**earch **E**ngine **A**bstraction **L**ayer with support to different search engines.

</div>

<br />
<br />

This package was highly inspired by [Doctrine DBAL](https://github.com/doctrine/dbal)
and [Flysystem](https://github.com/thephpleague/flysystem).

> **Note**:
> This is part of the `schranz-search/schranz-search` project create issues in the [main repository](https://github.com/schranz-search/schranz-search).

> **Warning**:
> This project is heavily under development and not ready for production.

## Installation

Use [composer](https://getcomposer.org/) for install the package:

```bash
composer require schranz-search/seal
```

Also install one of the listed adapters.

### List of adapters

The following adapters are available:

 - [MemoryAdapter](../seal-memory-adapter): `schranz-search/seal-memory-adapter
 - [ElasticsearchAdapter](../seal-elasticsearch-adapter): `schranz-search/seal-elasticsearch-adapter
 - [OpensearchAdapter](../seal-opensearch-adapter): `schranz-search/seal-opensearch-adapter
 - [MeilisearchAdapter](../seal-meilisearch-adapter): `schranz-search/seal-meilisearch-adapter
 - [AlgoliaAdapter](../seal-algolia-adapter): `schranz-search/seal-algolia-adapter
 - ... more coming soon

Additional Wrapper adapters:

 - [ReadWriteAdapter](../seal-read-write-adapter)
 - [MultiAdapter](../seal-multi-adapter)

## Usage

### Example Document

This should show the example document we want to create an index for:

```php
<?php

$document = [
    'id' => '1',
    'title' => 'New Blog',
    'header' => [
        'type' => 'image',
        'media' => 1,
    ],
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
    'footer' => [
        'title' => 'New Footer',
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
];
```

> Currently, you can use some kind of normalizer like symfony/serializer to convert an object to an array
> and back to an object at current state a Document Mapper or ODM package does not yet exist. If provided in future
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
- `OBJECT`: contains other fields nested in it
- `TYPED`: can define different fields by a type field

With exception to the `Identifier` type all other types can be defined as `multiple` to store a list of values.

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
];

$prefix = 'test_'; // to avoid conflicts the indexes can be prefixed

$newsIndex = new Index($prefix . 'news', $fields);

$schema = new Schema([
    'news' => $newsIndex,
]);
```

The schema is serializable, so it can be stored in any cache and loaded fast.

### Create the engine

The engine requires [an adapter](#list-of-adapters) and a previously created schema.

```php
<?php

use Elastic\Elasticsearch\ClientBuilder;
use Schranz\Search\SEAL\Adapter\Elasticsearch\ElasticsearchAdapter;
use Schranz\Search\SEAL\Engine;

$engine = new Engine(
    new ElasticsearchAdapter(
        ClientBuilder::create()->setHosts(['127.0.0.1:9200'])->build(),
    ),
    $schema,
);
```

The engine is the main entry point to interact with the search engine:

#### Write operations

All write operations returns nothing by default as some adapters are asynchron.

##### Schema operations

With the Schema methods all your indexes will be created or dropped:

```php
$engine->createSchema();
$engine->dropSchema();
```

It is possible to create or drop also only a specific index via:

```php
$engine->createIndex('news');
$engine->dropIndex('news');
```

It is also possible to check if an index already exists with:

```php
if (!$engine->existIndex('news')) {
   $engine->createIndex('news');
}
```

##### Document operations

To save a new or update an existing document you can use the `saveDocument` method:

```php
$engine->saveDocument('news', $document);
```

> Will overwrite existing document if document with same id already exists.

To delete a document you can use the `deleteDocument` method with the document identifier:

```php
$engine->deleteDocument('news', '1');
```

#### Search operations

##### Find a document

```php
$engine->getDocument('news', '1');
```

##### Search documents

```php
use Schranz\Search\SEAL\Search\Condition;

$documents = $engine->createSearchBuilder()
    ->addIndex('news')
    ->addFilter(new Condition\IdentifierCondition('1'))
    ->limit(1)
    ->offset(0)
    ->getResult();
```

> Condition is what in Elasticsearch are Queries and Filters.
> Not all conditions are supported by all adapters.
> Not all adapters support multiple indexes (e.g. [Meilisearch](https://github.com/schranz-search/schranz-search/issues/28)).
