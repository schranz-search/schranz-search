# Schranz Search SEAL Memory Adapter

The `MemoryAdapter` write the documents into an in-memory array.

> This is a subtree split of the `schranz-search/schranz-search` project create issues in the [main repository](https://github.com/schranz-search/schranz-search).

## Usage

It is mostly used for testing purposes and as a reference implementation.

```php
<?php

use Schranz\Search\SEAL\Adapter\Memory\MemoryAdapter;
use Schranz\Search\SEAL\Engine;

$engine = new Engine(
    new MemoryAdapter(),
    $schema,
);
```
