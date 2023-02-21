<div align="center">
    <img alt="Schranz Search Logo with a Seal on it with a magnifying glass" src="https://avatars.githubusercontent.com/u/120221538?s=400&v=5" width="200" height="200">
</div>

<h1 align="center">Schranz Search SEAL <br /> Meilisearch Adapter</h1>

<br />
<br />

The `OpensearchAdapter` write the documents into an [Opensearch](https://github.com/opensearch-project/OpenSearch) server instance.

> **Note**:
> This is part of the `schranz-search/schranz-search` project create issues in the [main repository](https://github.com/schranz-search/schranz-search).

> **Warning**:
> This project is heavily under development and not ready for production.

## Usage

The following code shows how to create an Engine using this Adapter:

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

Via DSN for your favorite framework:

```env
opensearch://127.0.0.1:9200
```

## Authors

- [Alexander Schranz](https://github.com/alexander-schranz/)
- [The Community Contributors](https://github.com/schranz-search/schranz-search/graphs/contributors)
