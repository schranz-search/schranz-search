# Schranz Search SEAL Meilisearch Adapter

The `MeilisearchAdapter` write the documents into a Meilisearch server instance.

> This is a subtree split of the `schranz-search/schranz-search` project create issues in the [main repository](https://github.com/schranz-search/schranz-search).

## Installation

Use [composer](https://getcomposer.org/) for install the package:

```bash
composer require schranz-search/seal schranz-search/seal-meilisearch-adapter
```

## Usage.

The following code shows how to create an Engine using this Adapter:

```php
<?php

use Meilisearch\Client;
use Schranz\Search\SEAL\Adapter\Meilisearch\MeilisearchAdapter;
use Schranz\Search\SEAL\Engine;

$client = new Client('http://127.0.0.1:7700');

$engine = new Engine(
    new MeilisearchAdapter($client),
    $schema,
);
```
