<div align="center">
    <img alt="Schranz Search Logo with a Seal on it with a magnifying glass" src="https://avatars.githubusercontent.com/u/120221538?s=400&v=6" width="200" height="200">
</div>

<div align="center">Logo created by <a href="https://cargocollective.com/meinewilma">Meine Wilma</a></div>

<h1 align="center">SEAL <br /> Algolia Adapter</h1>

<br />
<br />

The `AlgoliaAdapter` write the documents into the [Algolia](https://www.algolia.com/de/) SaaS.

> **Note**:
> This is part of the `schranz-search/schranz-search` project create issues in the [main repository](https://github.com/schranz-search/schranz-search).

> **Note**:
> This project is heavily under development and any feedback is greatly appreciated.

## Installation

Use [composer](https://getcomposer.org/) for install the package:

```bash
composer require schranz-search/seal schranz-search/seal-algolia-adapter
```

## Usage

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

Via DSN for your favorite framework:

```env
algolia://YourApplicationID:YourAdminAPIKey
```

## Authors

- [Alexander Schranz](https://github.com/alexander-schranz/)
- [The Community Contributors](https://github.com/schranz-search/schranz-search/graphs/contributors)
