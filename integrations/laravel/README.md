<div align="center">
    <img alt="Schranz Search Logo with a Seal on it with a magnifying glass" src="https://avatars.githubusercontent.com/u/120221538?s=400&v=6" width="200" height="200">
</div>

<div align="center">Logo created by <a href="https://cargocollective.com/meinewilma">Meine Wilma</a></div>

<h1 align="center">SEAL <br /> Laravel Integration</h1>

<br />
<br />

Integration of the Schranz Search — Search Engine Abstraction Layer (SEAL) into Laravel.

> **Note**:
> This is part of the `schranz-search/schranz-search` project create issues in the [main repository](https://github.com/schranz-search/schranz-search).

> **Note**:
> This project is heavily under development and any feedback is greatly appreciated.

## Installation

Use [composer](https://getcomposer.org/) for install the package:

```bash
composer require schranz-search/laravel-package
```

Also install one of the listed adapters.

### List of adapters

The following adapters are available:

 - [MemoryAdapter](../../packages/seal-memory-adapter): `schranz-search/seal-memory-adapter`
 - [ElasticsearchAdapter](../../packages/seal-elasticsearch-adapter): `schranz-search/seal-elasticsearch-adapter`
 - [OpensearchAdapter](../../packages/seal-opensearch-adapter): `schranz-search/seal-opensearch-adapter`
 - [MeilisearchAdapter](../../packages/seal-meilisearch-adapter): `schranz-search/seal-meilisearch-adapter`
 - [AlgoliaAdapter](../../packages/seal-algolia-adapter): `schranz-search/seal-algolia-adapter`
 - [SolrAdapter](../../packages/seal-solr-adapter): `schranz-search/seal-solr-adapter`
 - [RediSearchAdapter](../../packages/seal-redisearch-adapter): `schranz-search/seal-redisearch-adapter`
 - [TypesenseAdapter](../../packages/seal-typesense-adapter): `schranz-search/seal-typesense-adapter`
 - ... more coming soon

Additional Wrapper adapters:

 - [ReadWriteAdapter](../../packages/seal-read-write-adapter)
 - [MultiAdapter](../../packages/seal-multi-adapter)

Creating your own adapter? Add the [`seal-php-adapter`](https://github.com/topics/seal-php-adapter) Topic to your Github Repository.

## Usage

The following code shows how to configure the package:

```php
<?php

// config/schranz_search.php

return [
    /*
    |--------------------------------------------------------------------------
    | Schema configs
    |--------------------------------------------------------------------------
    |
    | Define different directories for the schema loader.
    */

    'schemas' => [
        'app' => [
            'dir' => resource_path('schemas'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | engines
    |--------------------------------------------------------------------------
    |
    | Directory where the latte templates can be found.
    */

    'engines' => [
        'default' => [
            'adapter' => env(
                'ENGINE_URL',
                'meilisearch://127.0.0.1:7700',
            ),
        ],
    ],
];
```

A more complex configuration can be here found:

```php
<?php

// config/schranz_search.php

return [
    /*
    |--------------------------------------------------------------------------
    | Schema configs
    |--------------------------------------------------------------------------
    |
    | Define different directories for the schema loader.
    */

    'schemas' => [
        'app' => [
            'dir' => resource_path('schemas') . '/app',
            'engine' => 'meilisearch',
        ],
        'other' => [
            'dir' => resource_path('schemas') . '/other',
            'engine' => 'algolia',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | engines
    |--------------------------------------------------------------------------
    |
    | Directory where the latte templates can be found.
    */

    'engines' => [
        'algolia' => [
            'adapter' => 'algolia://' . env('ALGOLIA_APPLICATION_ID') . ':' . env('ALGOLIA_ADMIN_API_KEY'),
        ],
        'elasticsearch' => [
            'adapter' => 'elasticsearch://127.0.0.1:9200',
        ],
        'meilisearch' => [
            'adapter' => 'meilisearch://127.0.0.1:7700',
        ],
        'memory' => [
            'adapter' => 'memory://',
        ],
        'opensearch' => [
            'adapter' => 'opensearch://127.0.0.1:9200',
        ],
        'redisearch' => [
            'adapter' => 'redis://supersecure@127.0.0.1:6379',
        ],
        'solr' => [
            'adapter' => 'solr://127.0.0.1:8983',
        ],
        'typesense' => [
            'adapter' => 'typesense://S3CR3T@127.0.0.1:8108',
        ],
        
        // ...
        'multi' => [
            'adapter' => 'multi://elasticsearch?adapters[]=opensearch',
        ],
        'read-write' => [
            'adapter' => 'read-write://elasticsearch?write=multi',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Schema prefix
    |--------------------------------------------------------------------------
    |
    | Define the prefix used for the index names to avoid conflicts.
    */

    'index_name_prefix' => '',
];
```

The default engine is available as `Engine`:

```php
class Some {
    public function __construct(
        private readonly \Schranz\Search\SEAL\EngineInterface $engine,
    ) {
    }
}
```

Multiple engines can be accessed via the `EngineRegistry`:

```php
class Some {
    private Engine $engine;

    public function __construct(
        private readonly \Schranz\Search\SEAL\EngineRegistry $engineRegistry,
    ) {
        $this->engine = $this->engineRegistry->get('algolia');
    }
}
```

Instead of constructor injection the `Laravel` integration provides also two `Facades`
for the above services:

- `Schranz\Search\Integration\Laravel\Facade\Engine`
- `Schranz\Search\Integration\Laravel\Facade\EngineRegistry`

How to create a `Schema` file and use your `Engine` can be found [SEAL Documentation](../../README.md#usage).

### Commands

The package provides the following commands:

**Create configured indexes**

```bash
php artisan schranz:search:index-create --help
```

**Drop configured indexes**

```bash
php artisan schranz:search:index-drop --help
```

## Authors

- [Alexander Schranz](https://github.com/alexander-schranz/)
- [The Community Contributors](https://github.com/schranz-search/schranz-search/graphs/contributors)
