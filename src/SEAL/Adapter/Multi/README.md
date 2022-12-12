# Schranz Search SEAL Multi Adapter

The `MultiAdater` allows to write into multiple adapters.

> This is a subtree split of the `schranz-search/schranz-search` project create issues in the [main repository](https://github.com/schranz-search/schranz-search).

## Usage

It is mostly used in combination with the `ReadWriteAdapter` to still
write into both indexes.

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
> Currently the `MultiAdapter` does not support the `search` method and so the `ReadWriteAdapter` is required.
