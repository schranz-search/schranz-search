# Schranz Search SEAL Algolia Adapter

The `AlgoliaAdapter` write the documents into a Algolia server instance.

> This is a subtree split of the `schranz-search/schranz-search` project create issues in the [main repository](https://github.com/schranz-search/schranz-search).

## Installation

Use [composer](https://getcomposer.org/) for install the package:

```bash
composer require schranz-search/seal schranz-search/seal-algolia-adapter
```

## Usage.

The following code shows how to create an Engine using this Adapter:

```php
<?php

use Algolia\AlgoliaSearch\SearchClient;
use Schranz\Search\SEAL\Adapter\Algolia\AlgoliaAdapter;
use Schranz\Search\SEAL\Engine;

$client = Algolia\AlgoliaSearch\SearchClient::create(
    'YourApplicationID',
    'YourAdminAPIKey',
);

$engine = new Engine(
    new AlgoliaAdapter($client),
    $schema,
);
```
