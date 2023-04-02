<div align="center">
    <img alt="Schranz Search Logo with a Seal on it with a magnifying glass" src="https://avatars.githubusercontent.com/u/120221538?s=400&v=5" width="200" height="200">
</div>

<h1 align="center">Schranz Search</h1>

<div align="center">

<strong>Monorepository</strong> for the **S**earch **E**ngine **A**bstraction **L**ayer with support to different search engines.

</div>

<br />
<br />

## SEAL

What `doctrine/dbal` is for `doctrine`, the `schranz-search/seal` is for `schranz-search` package.  
It provides a common interface to interact with different search engines.

This package was highly inspired by [Doctrine DBAL](https://github.com/doctrine/dbal) and [Flysystem](https://github.com/thephpleague/flysystem).

> **Note**:
> This project is heavily under development and any feedback is greatly appreciated.

## Installation

Use [composer](https://getcomposer.org/) for install the package:

```bash
composer require schranz-search/seal
```

Also install one of the listed adapters.

### Framework Integrations

The following framework integrations are available:

- [Laravel Module](integrations/laravel): `schranz-search/laravel-package`
- [Spiral Bridge](integrations/spiral): `schranz-search/spiral-bridge`
- [Symfony Bundle](integrations/symfony): `schranz-search/symfony-bundle`

### List of adapters

The following adapters are available:

- [MemoryAdapter](packages/seal-memory-adapter): `schranz-search/seal-memory-adapter`
- [ElasticsearchAdapter](packages/seal-elasticsearch-adapter): `schranz-search/seal-elasticsearch-adapter`
- [OpensearchAdapter](packages/seal-opensearch-adapter): `schranz-search/seal-opensearch-adapter`
- [MeilisearchAdapter](packages/seal-meilisearch-adapter): `schranz-search/seal-meilisearch-adapter`
- [AlgoliaAdapter](packages/seal-algolia-adapter): `schranz-search/seal-algolia-adapter`
- [SolrAdapter](packages/seal-solr-adapter): `schranz-search/seal-solr-adapter`
- [RediSearchAdapter](packages/seal-redisearch-adapter): `schranz-search/seal-redisearch-adapter`
- [TypesenseAdapter](packages/seal-typesense-adapter): `schranz-search/seal-typesense-adapter`
- ... more coming soon

Additional Wrapper adapters:

- [ReadWriteAdapter](packages/seal-read-write-adapter)
- [MultiAdapter](packages/seal-multi-adapter)

Creating your own adapter? Add the [`seal-php-adapter`](https://github.com/topics/seal-php-adapter) Topic to your GitHub Repository.

Missing an adapter? Let us know via a [GitHub Issue](https://github.com/schranz-search/schranz-search/issues).

## Research

This project started as a research project to find out how to create a common interface for different search engines.

<details>
   <summary>List of Search Engines</summary>

Here we collect different search engines which are around and could be interesting:

- [Elasticsearch](#elasticsearch) - [schranz-search/seal-elasticsearch-adapter](packages/seal-elasticsearch-adapter)
- [Opensearch](#opensearch) - [schranz-search/seal-opensearch-adapter](packages/seal-opensearch-adapter)
- [Meilisearch](#meilisearch) - [schranz-search/seal-meilisearch-adapter](packages/seal-meilisearch-adapter)
- [Algolia](#algolia) - [schranz-search/seal-algolia-adapter](packages/seal-algolia-adapter)
- [Solr](#solr) - [schranz-search/seal-solr-adapter](packages/seal-solr-adapter)
- [RediSearch](#redisearch) - [schranz-search/seal-redisearch-adapter](packages/seal-redisearch-adapter)
- [Typesense](#typesense) - [schranz-search/seal-typesense-adapter](packages/seal-typesense-adapter)
- [Zinc Labs](#zinc-labs) (work in progress [#79](https://github.com/schranz-search/schranz-search/pull/79))
- [Manticore Search](#manticore-search) (work in progress [#103](https://github.com/schranz-search/schranz-search/pull/103))
- [ZendSearch](#zendsearch)
- [Kailua Labs](#kailua-labs)
- [TnTSearch](#tntsearch)
- [Sonic](#sonic)
- [Vespa](#vespa)
- [Toshi](#toshi)
- [Quickwit](#quickwit)
- [nrtSearch](#nrtsearch)
- [MongoDB Atlas](#mongodb-atlas)
- [PostgreSQL Full Text Search](#postgresql-full-text-search)
- [MySQL Full Text Search](#mysql-full-text-search)
- [Sphinx Search](#sphinx-search)
- [Search.io](#searchio)
- [Azure Cognitive Search](#azure-cognitive-search)
- [Google Cloud Search](#google-cloud-search)
- [Amazon CloudSearch](#amazon-cloudsearch)
- [Gigablast](#gigablast)
- [Fess](#fess)
- [Bleve](#bleve)
- [Qdrant](#qdrant)
- [OpenAI](#openai)
- [Jina](#jina)

#### Some more research links:

- [https://alternativeto.net/software/meilisearch/](https://alternativeto.net/software/meilisearch/)
- [https://github.com/awesome-selfhosted/awesome-selfhosted#search-engines](https://github.com/awesome-selfhosted/awesome-selfhosted#search-engines)
- [https://help.openai.com/en/articles/6272952-search-transition-guide](https://help.openai.com/en/articles/6272952-search-transition-guide)
- [https://www.reddit.com/r/PHP/comments/104278m/research_what_search_services_engines_do_you_use/](https://www.reddit.com/r/PHP/comments/104278m/research_what_search_services_engines_do_you_use/)
- [https://github.com/doofinder/php-doofinder](https://github.com/doofinder/php-doofinder)
- [https://www.athenasearch.io/](https://www.athenasearch.io/)
- [https://www.g2.com/products/addsearch-site-search/reviews](https://www.g2.com/products/addsearch-site-search/reviews)
- [https://aws.amazon.com/de/athena/](https://aws.amazon.com/de/athena/) / [https://twitter.com/dr4goonis/status/1628451049013972993](https://twitter.com/dr4goonis/status/1628451049013972993)
- [https://milvus.io/](https://milvus.io/) / [https://twitter.com/milvusio](https://twitter.com/milvusio) / [https://packagist.org/packages/kaycn/milvusphp](https://packagist.org/packages/kaycn/milvusphp)
- [https://github.com/pgvector/pgvector](https://github.com/pgvector/pgvector)
- [https://vald.vdaas.org/](https://vald.vdaas.org/)
- [https://solr.apache.org/guide/solr/latest/query-guide/dense-vector-search.html](https://solr.apache.org/guide/solr/latest/query-guide/dense-vector-search.html)
- [https://github.com/facebookresearch/faiss](https://github.com/facebookresearch/faiss)

#### UI/UX related links:

- [https://design4users.com/design-search-in-user-interfaces/](https://design4users.com/design-search-in-user-interfaces/)

#### Optimization links:

- [https://sites.google.com/site/kevinbouge/stopwords-lists](https://sites.google.com/site/kevinbouge/stopwords-lists)
- [https://github.com/uschindler/german-decompounder](https://github.com/uschindler/german-decompounder)
- [https://symfony.com/blog/migrating-symfony-com-search-engine-to-meilisearch](https://symfony.com/blog/migrating-symfony-com-search-engine-to-meilisearch)

### Elasticsearch

Widely used search based on Java.

- Server: [Elasticsearch Server](https://github.com/elastic/elasticsearch)
- PHP Client: [Elasticsearch PHP](https://github.com/elastic/elasticsearch-php)

Implementation: [schranz-search/seal-elasticsearch-adapter](packages/seal-elasticsearch-adapter)

### Opensearch

Fork of Elasticsearch also written in Java.

- Server: [Opensearch Server](https://github.com/opensearch-project/OpenSearch)
- PHP Client: [Opensearch PHP](https://github.com/opensearch-project/opensearch-php)

Implementation: [schranz-search/seal-opensearch-adapter](packages/seal-opensearch-adapter)

### Meilisearch

A search engine written in Rust:

- Server: [MeiliSearch Server](https://github.com/meilisearch/meilisearch)
- PHP Client: [MeiliSearch PHP](https://github.com/meilisearch/meilisearch-php)

Implementation: [schranz-search/seal-meilisearch-adapter](packages/seal-meilisearch-adapter)

### Algolia

Is a search as SaaS provided via Rest APIs and SDKs:

- Server: No server only Saas [https://www.algolia.com/](https://www.algolia.com/)
- PHP Client: [Algolia PHP](https://github.com/algolia/algoliasearch-client-php)

Implementation: [schranz-search/seal-algolia-adapter](packages/seal-algolia-adapter)

### Solr

A search engine under the Apache Project based on Lucene written in Java:

- Server: [Solr Server](https://github.com/apache/solr)
- PHP Client: [Solarium PHP](https://github.com/solariumphp/solarium) seems to be a well maintained Client

Implementation: [schranz-search/seal-solr-adapter](packages/seal-solr-adapter)

### RediSearch

A search out of the house of the redis labs.

- Server: [RediSearch Server](https://github.com/RediSearch/RediSearch)
- PHP Client: [Unofficial RediSearch PHP](https://github.com/MacFJA/php-redisearch)

Implementation: [schranz-search/seal-redisearch-adapter](packages/seal-redisearch-adapter)

### Typesense

Describes itself as a alternative to Algolia and Elasticsearch written in C++.

- Server: [Typesense Server](https://github.com/typesense/typesense)
- PHP Client: [Typesense PHP](https://github.com/typesense/typesense-php)

Implementation: [schranz-search/seal-typesense-adapter](packages/seal-typesense-adapter)

### Zinc Labs

Zinc search describes itself as a lightweight alternative to Elasticsearch written in GoLang.

- Server: [Zinclabs Server](https://github.com/zinclabs/zinc)
- PHP Client: No PHP SDK currently: [https://github.com/zinclabs/zinc/issues/12](https://github.com/zinclabs/zinc/issues/12)

Implementation: work in progress [#79](https://github.com/schranz-search/schranz-search/pull/79)

### Manticore Search

Fork of Sphinx 2.3.2 in 2017, describes itself as an easy to use open source fast database for search.
Good alternative for Elasticsearch.

- Server: [Manticore Search Server](https://github.com/manticoresoftware/manticoresearch)
- PHP Client: [Manticore Search PHP Client](https://github.com/manticoresoftware/manticoresearch-php)

Implementation: work in progress [#103](https://github.com/schranz-search/schranz-search/pull/103)

### ZendSearch

A complete in PHP written implementation of the Lucene index. Not longer maintained:

- Implementation: [Zendsearch Implementation](https://github.com/handcraftedinthealps/zendsearch)

### Kailua Labs

Next-gen search made simple:

- Server: No server only Saas [https://www.kailualabs.com/](https://www.kailualabs.com/)

### TnTSearch

Another implementation of a Search index written in PHP. Not based on Lucene.

- Implementation: [TntSearch Implementation](https://github.com/teamtnt/tntsearch)

### Sonic

Describe itself as lightweight & schema-less search backend, an alternative to Elasticsearch that runs on a few MBs of RAM.

- Server: [Sonic Server](https://github.com/valeriansaliou/sonic)
- PHP Client: [Unoffical PHP Sonic](https://github.com/php-sonic/php-sonic) looks outdated and not well maintained

### Vespa

Describe itself as the open big data serving engine - Store, search, organize and make machine-learned inferences over big data at serving time.

- Server: [Vespa Server](https://github.com/vespa-engine/vespa)
  https://github.com/vespa-engine/vespa
- PHP Client: No client available only API based

### Toshi

A full-text search engine in rust. Toshi strives to be to Elasticsearch what [Tantivy Server](https://github.com/quickwit-oss/tantivy) is to Lucene:

- Server: [Toshi Server](https://github.com/toshi-search/Toshi)
- PHP Client: No client available only API based

### Quickwit

Describe itself as a cloud-native search engine for log management & analytics written in Rust. It is designed to be very cost-effective, easy to operate, and scale to petabytes.

- Server: [Quickwit Server](https://github.com/quickwit-oss/quickwit)
- PHP Client: No client available only API based

### nrtSearch

Describe itself as a high performance gRPC server, with optional REST APIs on top of Apache Lucene version 8.x source, exposing Lucene's core functionality over a simple gRPC based API.:

- Server: [nrtSearch Server](https://github.com/Yelp/nrtsearch)
- PHP Client: No client available only API based

### MongoDB Atlas

None open source search engine from MongoDB. It is a cloud based search engine.

- Server: [MongoDB Atlas](https://www.mongodb.com/atlas/search)
- PHP Client: [MongoDB Atlas PHP Client](https://www.mongodb.com/docs/drivers/php/#connect-to-mongodb-atlas)

### PostgreSQL Full Text Search

- Server: [PostgreSQL Server](https://www.postgresql.org/)
- PHP Client: No client use the [Full Text Feature](https://www.postgresql.org/docs/current/textsearch.html) the Database connection.

### MySQL Full Text Search

- Server: [MySQL Server](https://dev.mysql.com/)
- PHP Client: No client use the [Full Text Feature](https://dev.mysql.com/doc/refman/8.0/en/fulltext-search.html) the Database connection.

### Sphinx Search

An older search engine written in Python.

- Server: [Sphinx Search Server](http://sphinxsearch.com/downloads/current/)
- PHP Client: No official client available

### Search.io

A SaaS search engine, In the past they used the name for Sajari Site Search.
Lately [acquired by Algolia](https://twitter.com/SearchioHQ/status/1569298045959020549).

- Server: No server only Saas [Search.io Server](https://search.io/)
- PHP Client: [Official Search.io SDK for PHP](https://github.com/sajari/sdk-php)

### Azure Cognitive Search

A cloud based search from Microsoft Azure:

- Server: No server only SaaS [Azure Cognitive Search](https://learn.microsoft.com/en-us/azure/search/)
- PHP Client: No client available only [REST API](https://learn.microsoft.com/en-us/azure/search/search-get-started-rest)

### Google Cloud Search

A cloud based search from Google:

- Server: No server only SaaS [Google Cloud Search](https://workspace.google.com/products/cloud-search/)
- PHP Client: No client available only [REST API](https://developers.google.com/cloud-search/docs/reference/rest)

### Amazon CloudSearch

A cloud based search from Amazon:

- Server: No server only SaaS [Amazon CloudSearch](https://aws.amazon.com/de/cloudsearch/)
- PHP Client: No client available only [REST API](https://docs.aws.amazon.com/aws-sdk-php/v2/guide/service-cloudsearch.html)

### Gigablast

Describe itself as an open source web and enterprise search engine and spider/crawler
written in C++.

- Server: [Gigablast Server](https://github.com/gigablast/open-source-search-engine)
- PHP Client: No client available only [REST API](https://gigablast.com/api.html)

### Fess

Fess is very powerful and easily deployable Enterprise Search Server.

- Server: [Fess Server](https://github.com/codelibs/fess)

### Bleve

A modern text ndexing in go, supported and sponsored by Couchbase:

- Library only: [Bleve](https://github.com/blevesearch/bleve)

### Qdrant

A vector AI based search database:

- Server: [Qdrant Server](https://github.com/qdrant/qdrant)
- PHP Client: No client available only [REST API](https://qdrant.github.io/qdrant/redoc/index.html)

### OpenAI

OpenAi embeddings can also be used to create search engine:

- Docs Embeddings: [Embeddings](https://beta.openai.com/docs/api-reference/embeddings)
- Docs
  Search: [Deprecated Search Migratin Transition](https://help.openai.com/en/articles/6272952-search-transition-guide)

### Jina

Another vector based search engine:

- Server: [Jina Server](https://github.com/jina-ai/jina/)

</details>

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

> **Note**:
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

A more detailed schema definition can be made by defining which fields are
searchable, filterable and sortable. By default, all text fields are `searchable`
but no fields are `filterable` or `sortable`. At current state only text fields
can be `searchable`.

<details>
   <summary>Schema with searchable, filterable, sortable definitions:</summary>

```php
$fields = [
    'id' => new Field\IdentifierField('uuid'),
    'title' => new Field\TextField('title'),
    'header' => new Field\TypedField('header', 'type', [
        'image' => [
            'media' => new Field\IntegerField('media'),
        ],
        'video' => [
            'media' => new Field\TextField('media', searchable: false),
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
            'media' => new Field\TextField('media', searchable: false),
        ],
    ], multiple: true),
    'footer' => new Field\ObjectField('footer', [
        'title' => new Field\TextField('title'),
    ]),
    'created' => new Field\DateTimeField('created', filterable: true, sortable: true),
    'commentsCount' => new Field\IntegerField('commentsCount', filterable: true, sortable: true),
    'rating' => new Field\FloatField('rating', filterable: true, sortable: true),
    'comments' => new Field\ObjectField('comments', [
        'email' => new Field\TextField('email', searchable: false),
        'text' => new Field\TextField('text'),
    ], multiple: true),
    'tags' => new Field\TextField('tags', multiple: true, filterable: true),
    'categoryIds' => new Field\IntegerField('categoryIds', multiple: true, filterable: true),
];
```

</details>

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

The library provides different `Condition` classes to build a query for the search engines.

> Condition is what in Elasticsearch are Queries and Filters.

> **Warning**:
> Not all conditions are supported by all adapters.

###### SearchCondition

The `SearchCondition` can be used for a basic search query:

```php
use Schranz\Search\SEAL\Search\Condition;

$documents = $engine->createSearchBuilder()
    ->addIndex('news')
    ->addFilter(new Condition\SearchCondition('New Blog'))
    ->getResult();
```

###### Pagination

Use `limit` and `offset` methods to paginate the results.:

```php
use Schranz\Search\SEAL\Search\Condition;

$documents = $engine->createSearchBuilder()
    ->addIndex('news')
    ->addFilter(new Condition\SearchCondition('New Blog'))
    ->limit(10)
    ->offset(0)
    ->getResult();
```

###### Sorting

Normally search results are ordered by best match but it possible to
force a specific order via `addSortBy` method:

```php
use Schranz\Search\SEAL\Search\Condition;

$documents = $engine->createSearchBuilder()
    ->addIndex('news')
    ->addSortBy('rating', 'desc')
    ->getResult();
```

###### Multi Index

To search in multiple indexes you can use the `addIndex` method multiple times.

```php
use Schranz\Search\SEAL\Search\Condition;

$documents = $engine->createSearchBuilder()
    ->addIndex('news')
    ->addIndex('blog')
    ->addFilter(new Condition\SearchCondition('New Blog'))
    ->getResult();
```

> **Warning**:
> Not all adapters support multiple indexes at the same time (e.g. [Meilisearch](https://github.com/schranz-search/schranz-search/issues/28), [Algolia](https://github.com/schranz-search/schranz-search/issues/41)).

###### IdentifierCondition

The `IdentifierCondition` can be used to load a specific document:

```php
use Schranz\Search\SEAL\Search\Condition;

$documents = $engine->createSearchBuilder()
    ->addIndex('news')
    ->addFilter(new Condition\IdentifierCondition('1'))
    ->limit(1)
    ->offset(0)
    ->getResult();
```

If no additional filters are required also the [`getDocument`](#find-a-document) function can be used instead:

```php
$document = $engine->getDocument('news', '1');
```

###### EqualCondition

The `EqualCondition` can be used to load documents matching a specific value:

```php
use Schranz\Search\SEAL\Search\Condition;

$documents = $engine->createSearchBuilder()
    ->addIndex('news')
    ->addFilter(new Condition\EqualCondition('tags', 'UI'))
    ->getResult();
```

The first parameter is the `field` and the second the `value` which need to match.

> **Note**:  
> For filtering by `ObjectField`s use `<object_name>.<field_name>` as field e.g.:  
> `new Condition\NotEqualCondition('header.media', 1)`.

> **Note**:  
> For filtering by `TypedField`s use `<typed_name>.<type_name>.<field_name>` as field e.g.:  
> `new Condition\NotEqualCondition('blocks.text.media', 1)`.

###### NotEqualCondition

The `NotEqualCondition` can be used to load documents matching not a specific value:

```php
use Schranz\Search\SEAL\Search\Condition;

$documents = $engine->createSearchBuilder()
    ->addIndex('news')
    ->addFilter(new Condition\NotEqualCondition('tags', 'UI'))
    ->getResult();
```

The first parameter is the `field` and the second the `value` which need not match.

> **Note**:  
> For filtering by `ObjectField`s use `<object_name>.<field_name>` as field e.g.:  
> `new Condition\NotEqualCondition('header.media', 1)`.

> **Note**:  
> For filtering by `TypedField`s use `<typed_name>.<type_name>.<field_name>` as field e.g.:  
> `new Condition\NotEqualCondition('blocks.text.media', 1)`.

###### GreaterThanCondition

The `GreaterThanCondition` can be used to load documents where a field is `>` a specific value:

```php
use Schranz\Search\SEAL\Search\Condition;

$documents = $engine->createSearchBuilder()
    ->addIndex('news')
    ->addFilter(new Condition\GreaterThanCondition('rating', 3.5))
    ->getResult();
```

The first parameter is the `field` and the second the `value` which need the field need to greater than.

> **Note**:  
> For filtering by `ObjectField`s use `<object_name>.<field_name>` as field e.g.:  
> `new Condition\NotEqualCondition('header.media', 1)`.

> **Note**:  
> For filtering by `TypedField`s use `<typed_name>.<type_name>.<field_name>` as field e.g.:  
> `new Condition\NotEqualCondition('blocks.text.media', 1)`.

###### GreaterThanEqualCondition

The `GreaterThanEqualCondition` can be used to load documents where a field is `>=` a specific value:

```php
use Schranz\Search\SEAL\Search\Condition;

$documents = $engine->createSearchBuilder()
    ->addIndex('news')
    ->addFilter(new Condition\GreaterThanEqualCondition('rating', 3.5))
    ->getResult();
```

The first parameter is the `field` and the second the `value` which need the field need to greater than or equal.

> **Note**:  
> For filtering by `ObjectField`s use `<object_name>.<field_name>` as field e.g.:  
> `new Condition\NotEqualCondition('header.media', 1)`.

> **Note**:  
> For filtering by `TypedField`s use `<typed_name>.<type_name>.<field_name>` as field e.g.:  
> `new Condition\NotEqualCondition('blocks.text.media', 1)`.

###### LessThanCondition

The `LessThanCondition` can be used to load documents where a field is `<` a specific value:

```php
use Schranz\Search\SEAL\Search\Condition;

$documents = $engine->createSearchBuilder()
    ->addIndex('news')
    ->addFilter(new Condition\LessThanCondition('rating', 3.5))
    ->getResult();
```

The first parameter is the `field` and the second the `value` which need the field need to less than.

> **Note**:  
> For filtering by `ObjectField`s use `<object_name>.<field_name>` as field e.g.:  
> `new Condition\NotEqualCondition('header.media', 1)`.

> **Note**:  
> For filtering by `TypedField`s use `<typed_name>.<type_name>.<field_name>` as field e.g.:  
> `new Condition\NotEqualCondition('blocks.text.media', 1)`.

###### LessThanEqualCondition

The `LessThanEqualCondition` can be used to load documents where a field is `<=` a specific value:

```php
use Schranz\Search\SEAL\Search\Condition;

$documents = $engine->createSearchBuilder()
    ->addIndex('news')
    ->addFilter(new Condition\LessThanEqualCondition('rating', 3.5))
    ->getResult();
```

The first parameter is the `field` and the second the `value` which need the field need to less than or equal.

> **Note**:  
> For filtering by `ObjectField`s use `<object_name>.<field_name>` as field e.g.:  
> `new Condition\NotEqualCondition('header.media', 1)`.

> **Note**:  
> For filtering by `TypedField`s use `<typed_name>.<type_name>.<field_name>` as field e.g.:  
> `new Condition\NotEqualCondition('blocks.text.media', 1)`.

## Similar Projects

Following projects in the past target similar problem:

- [https://github.com/nresni/Ariadne](https://github.com/nresni/Ariadne) (Solr, Elasticsearch, Zendsearch: outdated 12 years ago)
- [https://github.com/massiveart/MassiveSearchBundle](https://github.com/massiveart/MassiveSearchBundle) (ZendSearch, Elasticsearch)
- [https://github.com/laravel/scout](https://github.com/laravel/scout) (Algolia, Meilisearch)

## Authors

- [Alexander Schranz](https://github.com/alexander-schranz/)
- [The Community Contributors](https://github.com/schranz-search/schranz-search/graphs/contributors)
