<div align="center">
    <img alt="Schranz Search Logo with a Seal on it with a magnifying glass" src="https://avatars.githubusercontent.com/u/120221538?s=400&v=5" width="200" height="200">
</div>

<h1 align="center">Schranz Search SEAL <br /> Memory Adapter</h1>

<br />
<br />

The `MemoryAdapter` write the documents into an in-memory array.

> **Note**:
> This is part of the `schranz-search/schranz-search` project create issues in the [main repository](https://github.com/schranz-search/schranz-search).

> **Warning**:
> This project is heavily under development and not ready for production.

## Installation

Use [composer](https://getcomposer.org/) for install the package:

```bash
composer require schranz-search/seal schranz-search/seal-memory-adapter
```

## Usage

It is mostly used for testing purposes and as a reference implementation.

The following code shows how to create an Engine using this Adapter:

```php
<?php

use Schranz\Search\SEAL\Adapter\Memory\MemoryAdapter;
use Schranz\Search\SEAL\Engine;

$engine = new Engine(
    new MemoryAdapter(),
    $schema,
);
```

## Authors

- [Alexander Schranz](https://github.com/alexander-schranz/)
- [The Community Contributors](https://github.com/schranz-search/schranz-search/graphs/contributors)
