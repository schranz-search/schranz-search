<div align="center">
    <img alt="Schranz Search Logo with a Seal on it with a magnifying glass" src="https://avatars.githubusercontent.com/u/120221538?s=400&v=5" width="200" height="200">
</div>

<h1 align="center">Schranz Search SEAL <br /> Spiral Integration</h1>

<br />
<br />

Integration of the Schranz Search — Search Engine Abstraction Layer (SEAL) into Spiral.

> **Note**:
> This is a part of the `schranz-search/schranz-search` project. If you have any issues or concerns, feel free to create
> a ticket in the [main repository](https://github.com/schranz-search/schranz-search).

> **Note**:
> This project is heavily under development and any feedback is greatly appreciated.

## Installation

Use [composer](https://getcomposer.org/) for install the package:

```bash
composer require schranz-search/spiral-bridge
```

After successful installation of the package, it is necessary to register
the `Schranz\Search\Integration\Spiral\Bootloader\SearchBootloader` bootloader:

```php
// app/src/Application/Kernel.php

protected const LOAD = [
    \Schranz\Search\Integration\Spiral\Bootloader\SearchBootloader::class,
];
```

Now you can install desired adapters for your application. List of available adapters can be found below:

### List of available adapters

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

> **Note**
> If you're planning on creating your own adapter, don't forget to add
> the [`seal-php-adapter`](https://github.com/topics/seal-php-adapter) Topic to your GitHub Repository. This will help
> others find your adapter more easily.

## Configuration

At first, you need to create a configuration file `app/config/schranz_search.php` that helps you to configure search
schemas and engines. Below you can find an example of a simple configuration file:

```php
<?php
// app/config/schranz_search.php

return [
    'schemas' => [
        'app' => [
            'dir' => directory('app'), 'schemas',
        ],
    ],
    'engines' => [
        'default' => [
            'adapter' => 'meilisearch://127.0.0.1:7700',
        ],
    ],
];
```

> **Warning**
> When defining the `engines` section, make sure to use the `default` key to set the default engine. This default engine
> can then be accessed through the `Schranz\Search\SEAL\Engine` class. If you don't explicitly define a default engine,
> the first engine listed will be used as the default.

Here is also an example of more complex configuration:

```php
<?php
// app/config/schranz_search.php

return [
    'schemas' => [
        'app' => [
            'dir' => directory('app'), 'schemas/app',
            'engine' => 'meilisearch',
        ],
        'other' => [
            'dir' => directory('app'), 'schemas/other',
            'engine' => 'algolia',
        ],
    ],
    'engines' => [
        'algolia' => [
            'adapter' => 'algolia://' . env('ALGOLIA_APPLICATION_ID') . ':' . env('ALGOLIA_ADMIN_API_KEY'),
        ],
        'elasticsearch' => [
            'adapter' => 'elasticsearch://' . env('ELASTICSEARCH_HOST', '127.0.0.1:9200'),
        ],
        'meilisearch' => [
            'adapter' => 'meilisearch://' . env('MEILISEARCH_HOST', '127.0.0.1:7700'),
        ],
        'memory' => [
            'adapter' => 'memory://',
        ],
        'opensearch' => [
            'adapter' => 'opensearch://' . env('OPENSEARCH_HOST', '127.0.0.1:9200'),
        ],
        'redisearch' => [
            'adapter' => 'redis://' . env('REDISEARCH_HOST', 'supersecure@127.0.0.1:6379'),
        ],
        'solr' => [
            'adapter' => 'solr://' . env('SOLR_HOST', '127.0.0.1:8983'),
        ],
        'typesense' => [
            'adapter' => 'typesense://' . env('TYPESENSE_HOST', 'S3CR3T@127.0.0.1:8108'),
        ],

        // ...
        'multi' => [
            'adapter' => 'multi://elasticsearch?adapters[]=opensearch',
        ],
        'read-write' => [
            'adapter' => 'read-write://elasticsearch?write=multi',
        ],
    ],
];
```

## Usage

You can access a default engine via the `Schranz\Search\SEAL\Engine` class:

```php
final class UsersSearchService {
    public function __construct(
        private readonly \Schranz\Search\SEAL\Engine $engine,
    ) {
    }
    
    // ...
}
```

Multiple engines can be accessed via the `EngineRegistry`:

```php
use Schranz\Search\SEAL\EngineRegistry;
use Schranz\Search\SEAL\Engine;

final class UsersSearchService 
{
    private Engine $algoliaEngine;
    private Engine $meilisearchEngine;

    public function __construct(
        EngineRegistry $registry,
        private readonly UserRepository $users,
    ) {
        $this->algoliaEngine = $registry->get('algolia');
        $this->meilisearchEngine = $registry->get('meilisearch');
    }
    
    // ...
}
```

For more information on creating a `Schema` file and using your `Engine`, please refer to
the [SEAL Documentation](../../README.md#usage). This guide will provide you with detailed instructions and examples to
help you get started.

### Commands

The package offers the following commands:

**To create configured indexes, run the following command:**

```bash
php app.php schranz:search:index-create --help
```

**To drop configured indexes, run the following command:**

```bash
php app.php schranz:search:index-drop --help
```

These commands will help you manage your indexes efficiently.

## Authors

- [Alexander Schranz](https://github.com/alexander-schranz/)
- [The Community Contributors](https://github.com/schranz-search/schranz-search/graphs/contributors)
