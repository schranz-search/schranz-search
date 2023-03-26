<div align="center">
    <img alt="Schranz Search Logo with a Seal on it with a magnifying glass" src="https://avatars.githubusercontent.com/u/120221538?s=400&v=5" width="200" height="200">
</div>

<h1 align="center">Schranz Search SEAL <br /> Elasticsearch Adapter</h1>

<br />
<br />

The `ElasticsearchAdapter` write the documents into an [Elasticsearch](https://github.com/elastic/elasticsearch) server instance.

> **Note**:
> This is part of the `schranz-search/schranz-search` project create issues in the [main repository](https://github.com/schranz-search/schranz-search).

> **Note**:
> This project is heavily under development and any feedback is greatly appreciated.

# Schranz Search SEAL Elasticsearch Adapter

The `ElasticsearchAdapter` write the documents into an Elasticsearch server instance.

> This is a subtree split of the `schranz-search/schranz-search` project create issues in the [main repository](https://github.com/schranz-search/schranz-search).

## Installation

Use [composer](https://getcomposer.org/) for install the package:

```bash
composer require schranz-search/seal schranz-search/seal-elasticsearch-adapter
```

## Usage

The following code shows how to create an Engine using this Adapter:

```php
<?php

use Elastic\Elasticsearch\ClientBuilder;
use Schranz\Search\SEAL\Adapter\Elasticsearch\ElasticsearchAdapter;
use Schranz\Search\SEAL\Engine;

$client = ClientBuilder::create()->setHosts([
    '127.0.0.1:9200'
])->build()

$engine = new Engine(
    new ElasticsearchAdapter($client),
    $schema,
);
```

Via DSN for your favorite framework:

```env
elasticsearch://127.0.0.1:9200
```

## Authors

- [Alexander Schranz](https://github.com/alexander-schranz/)
- [The Community Contributors](https://github.com/schranz-search/schranz-search/graphs/contributors)
