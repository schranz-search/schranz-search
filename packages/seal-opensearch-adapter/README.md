# Schranz Search SEAL Opensearch Adapter

The `OpensearchAdapter` write the documents into an Opensearch server instance.

> This is a subtree split of the `schranz-search/schranz-search` project create issues in the [main repository](https://github.com/schranz-search/schranz-search).

## Installation

Use [composer](https://getcomposer.org/) for install the package:

```bash
composer require schranz-search/seal schranz-search/seal-opensearch-adapter
```

## Usage

It is mostly used for testing purposes and as a reference implementation.

```php
<?php

use OpenSearch\ClientBuilder;
use Schranz\Search\SEAL\Adapter\Opensearch\OpensearchAdapter;
use Schranz\Search\SEAL\Engine;

$client = ClientBuilder::create()->setHosts([
    '127.0.0.1:9200'
])->build()

$engine = new Engine(
    new OpensearchAdapter($client),
    $schema,
);
```
