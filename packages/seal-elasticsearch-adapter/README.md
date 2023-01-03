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
