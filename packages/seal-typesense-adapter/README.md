<div align="center">
    <img alt="Schranz Search Logo with a Seal on it with a magnifying glass" src="https://avatars.githubusercontent.com/u/120221538?s=400&v=5" width="200" height="200">
</div>

<h1 align="center">Schranz Search SEAL <br /> Typesense Adapter</h1>

<br />
<br />

The `TypesenseAdapter` write the documents into a [Typesense](https://github.com/typesense/typesense) server instance.

> **Note**:
> This is part of the `schranz-search/schranz-search` project create issues in the [main repository](https://github.com/schranz-search/schranz-search).

> **Warning**:
> This project is heavily under development and not ready for production.

## Installation

Use [composer](https://getcomposer.org/) for install the package:

```bash
composer require schranz-search/seal schranz-search/seal-typesense-adapter
```

## Usage.

The following code shows how to create an Engine using this Adapter:

```php
<?php

use Http\Client\Curl\Client as CurlClient;
use Http\Discovery\Psr17FactoryDiscovery;
use Schranz\Search\SEAL\Adapter\Typesense\TypesenseAdapter;
use Schranz\Search\SEAL\Engine;
use Typesense\Client;

$client = new Client(
    [
        'api_key' => 'S3CR3T',
        'nodes' => [
            [
                'host' => '127.0.0.1',
                'port' => '8108',
                'protocol' => 'http',
            ],
        ],
        'client' => new CurlClient(Psr17FactoryDiscovery::findResponseFactory(), Psr17FactoryDiscovery::findStreamFactory()),
    ]
);

$engine = new Engine(
    new TypesenseAdapter($client),
    $schema,
);
```

## Authors

- [Alexander Schranz](https://github.com/alexander-schranz/)
- [The Community Contributors](https://github.com/schranz-search/schranz-search/graphs/contributors)
