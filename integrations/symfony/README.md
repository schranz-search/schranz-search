<div align="center">
    <img alt="Schranz Search Logo with a Seal on it with a magnifying glass" src="https://avatars.githubusercontent.com/u/120221538?s=400&v=5" width="200" height="200">
</div>

<h1 align="center">Schranz Search SEAL <br /> Symfony Integration</h1>

<br />
<br />

Integration of the Schranz Search — Search Engine Abstraction Layer (SEAL) into Symfony.

> **Note**:
> This is part of the `schranz-search/schranz-search` project create issues in the [main repository](https://github.com/schranz-search/schranz-search).

> **Note**:
> This project is heavily under development and any feedback is greatly appreciated.

## Installation

Use [composer](https://getcomposer.org/) for install the package:

```bash
composer require schranz-search/symfony-integration
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

## Usage.

The following code shows how to configure the package:

```yaml
# config/packages/schranz_search.yaml

schranz_search:
    connections:
        algolia:
            dsn: 'algolia://%env(ALGOLIA_APPLICATION_ID)%:%env(ALGOLIA_ADMIN_API_KEY)%'
        elasticsearch:
            dsn: 'elasticsearch://127.0.0.1:9200'
        meilisearch:
            dsn: 'meilisearch://127.0.0.1:7700'
        memory:
            dsn: 'memory://'
        opensearch:
            dsn: 'opensearch://127.0.0.1:9200'
        redisearch:
            dsn: 'redis://supersecure@127.0.0.1:6379'
        solr:
            dsn: 'solr://127.0.0.1:8983'
        typesense:
            dsn: 'typesense://S3CR3T@127.0.0.1:8108'

        # ...
        multi:
            dsn: 'multi://elasticsearch?adapters[]=opensearch'
        read-write:
            dsn: 'read-write://elasticsearch?write=multi'
```

## Authors

- [Alexander Schranz](https://github.com/alexander-schranz/)
- [The Community Contributors](https://github.com/schranz-search/schranz-search/graphs/contributors)
