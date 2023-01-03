# Schranz Search SEAL Read Write Adapter

The `ReadWriteAdapter` allows to use one adapter instance for reading
and one for writing. This is useful if you want to reindex something
without a downtime.

> This is a subtree split of the `schranz-search/schranz-search` project create issues in the [main repository](https://github.com/schranz-search/schranz-search).

## Installation

Use [composer](https://getcomposer.org/) for install the package:

```bash
composer require schranz-search/seal schranz-search/seal-read-write-adapter
```

## Usage

To use the adapter an instance of `ReadWriteAdapter` need to be created
which get a `$readAdapter` and `$writeAdapter` which are instances of the
`AdapterInterface`.

The following code shows how to create an Engine using this Adapter:

```php
<?php

use Schranz\Search\SEAL\Adapter\Elasticsearch\ElasticsearchAdapter;
use Schranz\Search\SEAL\Adapter\ReadWrite\ReadWriteAdapter;
use Schranz\Search\SEAL\Engine;

$readAdapter = new ElasticsearchAdapter(/* .. */); // can be any adapter
$writeAdapter = new ElasticsearchAdapter(/* .. */); // can be any adapter

$engine = new Engine(
    new ReadWriteAdapter(
        $readAdapter,
        $writeAdapter
    ),
    $schema,
);
```

> **Note**
> Read a document and partial update it based on the read document should be avoided
> when using this adapter, as the read document could already be outdated. So always
> fully update the document and never do based on read documents.
> Have a look at the `MultiAdapter` to write into read and write adapter.
