<div align="center">
    <img alt="Schranz Search Logo with a Seal on it with a magnifying glass" src="https://avatars.githubusercontent.com/u/120221538?s=400&v=5" width="200" height="200">
</div>

<h1 align="center">Schranz Search SEAL <br /> Multi Adapter</h1>

<br />
<br />

The `MultiAdapter` allows to write into multiple adapters.

> **Note**:
> This is part of the `schranz-search/schranz-search` project create issues in the [main repository](https://github.com/schranz-search/schranz-search).

> **Warning**:
> This project is heavily under development and not ready for production.

## Usage

It is mostly used in combination with the `ReadWriteAdapter` to still write into both indexes.

The following code shows how to create an Engine using this Adapter:

```php
<?php

use Schranz\Search\SEAL\Adapter\Elasticsearch\ElasticsearchAdapter;
use Schranz\Search\SEAL\Adapter\Multi\MultiAdapter;
use Schranz\Search\SEAL\Adapter\ReadWrite\ReadWriteAdapter;
use Schranz\Search\SEAL\Engine;

$readAdapter = new ElasticsearchAdapter(/* .. */); // can be any adapter
$writeAdapter = new ElasticsearchAdapter(/* .. */); // can be any adapter

$engine = new Engine(
    new ReadWriteAdapter(
        $readAdapter,
        new MultiAdapter(
            $readAdapter,
            $writeAdapter,
        ),
    ),
    $schema,
);
```

> **Note**
> The `MultiAdapter` does not support the `search` method and so the `ReadWriteAdapter` is required.
