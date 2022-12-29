# Schranz Search SEAL Elasticsearch Adapter

The `ElasticsearchAdapter` write the documents into an Elasticsearch server instance.

> This is a subtree split of the `schranz-search/schranz-search` project create issues in the [main repository](https://github.com/schranz-search/schranz-search).

## Usage

It is mostly used for testing purposes and as a reference implementation.

```php
<?php

use Schranz\Search\SEAL\Adapter\Elasticsearch\ElasticsearchAdapter;
use Schranz\Search\SEAL\Engine;

$client = Elasticsearch\ClientBuilder::create()->build();

$engine = new Engine(
    new Elasticsearch($client),
    $schema,
);
```
