<div align="center">
    <img alt="Schranz Search Logo with a Seal on it with a magnifying glass" src="https://avatars.githubusercontent.com/u/120221538?s=400&v=6" width="200" height="200">
</div>

<div align="center">Logo created by <a href="https://cargocollective.com/meinewilma">Meine Wilma</a></div>

<h1 align="center">SEAL <br /> RediSearch Adapter</h1>

<br />
<br />

The `RediSearchAdapter` write the documents into a [RediSearch](https://redis.io/docs/stack/search/) server instance. The Redis Server requires to run with the RedisSearch and JSON module.

> **Note**:
> This is part of the `schranz-search/schranz-search` project create issues in the [main repository](https://github.com/schranz-search/schranz-search).

> **Note**:
> This project is heavily under development and any feedback is greatly appreciated.

## Installation

Use [composer](https://getcomposer.org/) for install the package:

```bash
composer require schranz-search/seal schranz-search/seal-redisearch-adapter
```

## Usage.

The following code shows how to create an Engine using this Adapter:

```php
<?php

use Redis;
use Schranz\Search\SEAL\Adapter\RediSearch\RediSearchAdapter;
use Schranz\Search\SEAL\Engine;

$redis = new Redis([
    'host' => '127.0.0.1',
    'port' => 6379,
    'auth' => ['phpredis', 'phpredis'],
]);

$engine = new Engine(
    new RediSearchAdapter($redis),
    $schema,
);
```

Via DSN for your favorite framework:

```env
redis://127.0.0.1:6379
redis://supersecure@127.0.0.1:6379
redis://phpredis:phpredis@127.0.0.1:6379
```

The `ext-redis` and `ext-json` PHP extension is required for this adapter.  
The `Redisearch` and `RedisJson` module is required for the Redis Server.

## Authors

- [Alexander Schranz](https://github.com/alexander-schranz/)
- [The Community Contributors](https://github.com/schranz-search/schranz-search/graphs/contributors)
