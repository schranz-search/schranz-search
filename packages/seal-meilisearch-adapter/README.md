<div align="center">
    <img alt="Schranz Search Logo with a Seal on it with a magnifying glass" src="https://avatars.githubusercontent.com/u/120221538?s=400&v=5" width="200" height="200">
</div>

<h1 align="center">Schranz Search SEAL <br /> Meilisearch Adapter</h1>

<br />
<br />

The `MeilisearchAdapter` write the documents into a [Meilisearch](https://github.com/meilisearch/meilisearch) server instance.

> **Note**:
> This is part of the `schranz-search/schranz-search` project create issues in the [main repository](https://github.com/schranz-search/schranz-search).

> **Note**:
> This project is heavily under development and any feedback is greatly appreciated.

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

Via DSN for your favorite framework:

```env
meilisearch://127.0.0.1:7700
meilisearch://apiKey@127.0.0.1:7700
```

## Authors

- [Alexander Schranz](https://github.com/alexander-schranz/)
- [The Community Contributors](https://github.com/schranz-search/schranz-search/graphs/contributors)
