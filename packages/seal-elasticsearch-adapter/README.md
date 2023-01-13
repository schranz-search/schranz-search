<div align="center">
    <img alt="Schranz Search Logo with a Seal on it with a magnifying glass" src="https://avatars.githubusercontent.com/u/120221538?s=400&v=5" width="200" height="200">
</div>

<h1 align="center">Schranz Search SEAL <br /> Elasticsearch Adapter</h1>

<br />
<br />

The `ElasticsearchAdapter` write the documents into an [Elasticsearch](https://github.com/elastic/elasticsearch) server instance.

> **Note**:
> This is part of the `schranz-search/schranz-search` project create issues in the [main repository](https://github.com/schranz-search/schranz-search).

> **Warning**:
> This project is heavily under development and not ready for production.

# Schranz Search SEAL Elasticsearch Adapter

The `ElasticsearchAdapter` write the documents into an Elasticsearch server instance.

> This is a subtree split of the `schranz-search/schranz-search` project create issues in the [main repository](https://github.com/schranz-search/schranz-search).

## Installation

Use [composer](https://getcomposer.org/) for install the package:

```bash
composer require schranz-search/seal schranz-search/seal-elasticsearch-adapter
```

## Usage

The following code shows how to create an Engine using this Adapter:

```php
<?php

use Elastic\Elasticsearch\ClientBuilder;
use Schranz\Search\SEAL\Adapter\Elasticsearch\ElasticsearchAdapter;
use Schranz\Search\SEAL\Engine;

$client = ClientBuilder::create()->setHosts([
    '127.0.0.1:9200'
])->build()

$engine = new Engine(
    new ElasticsearchAdapter($client),
    $schema,
);
```

## Schema Mapping

This shows how the [Seal Schema](../seal/README.md#schema) is mapped to Elasticsearch.

There is no diff between sortable and filterable fields so that cases are not specific listed.

| searchable | filterable / sortable | description                                                                 |
|------------|-----------------------|-----------------------------------------------------------------------------|
| yes        | no                    | Searchable but can not be used for filter or sorting                        |
| no         | yes                   | Is not keep in mind for search score but can be used for filter and sorting |
| yes        | yes                   | Is keep in mind for search score and can be used for filter and sorting     |
| no         | no                    | Just stored no index, analyzing required                                    |

### IdentifierField

Field:

```php
new IdentifierField('id');
```

Mapping:

```json
{
    "id": {
        "type": "keyword",
        "index": true,
        "doc_values": true
    }
}
```

Also the identifier is used as elasticsearch internal `_id` field.

### TextField

Field:

```php
new TextField('text_a', searchable: true, filterable: false, sortable: false), // default
new TextField('text_b', searchable: false, filterable: true, sortable: true),
```

Mapping:

```json
{
    "text_a": {
        "type": "text",
        "index": true,
        "doc_values": false
    },
    "text_b": {
        "type": "text",
        "index": true,
        "doc_values": true,
        "field": {
            "raw": {
                "type": "keyword",
                "index": true,
                "doc_values": true
            }
        }
    }
}
```

### BooleanField

Field:

```php
new BooleanField('bool_a', searchable: true, filterable: false, sortable: false), // default
new BooleanField('bool_b', searchable: false, filterable: true, sortable: true),
```

Mapping:

```json
{
    "bool_a": {
        "type": "boolean",
        "index": true,
        "doc_values": false
    },
    "bool_b": {
        "type": "boolean",
        "index": true,
        "doc_values": true
    }
}
```

### DateTimeField

Field:

```php
new DateTimeField('date_a', searchable: true, filterable: false, sortable: false), // default
new DateTimeField('date_b', searchable: false, filterable: true, sortable: true),
```

Mapping:

```json
{
    "date_a": {
        "type": "date",
        "index": true,
        "doc_values": false
    },
    "date_b": {
        "type": "date",
        "index": true,
        "doc_values": true
    }
}
```

### IntegerField

Field:

```php
new IntegerField('int_a', searchable: true, filterable: false, sortable: false), // default
new IntegerField('int_b', searchable: false, filterable: true, sortable: true),
```

Mapping:

```json
{
    "int_a": {
        "type": "integer",
        "index": true,
        "doc_values": false
    },
    "int_b": {
        "type": "integer",
        "index": true,
        "doc_values": true
    }
}
```

### FloatField

Field:

```php
new FloatField('float_a', searchable: true, filterable: false, sortable: false), // default
new FloatField('float_b', searchable: false, filterable: true, sortable: true),
```

Mapping:

```json
{
    "float_a": {
        "type": "float",
        "index": true,
        "doc_values": false
    },
    "float_b": {
        "type": "float",
        "index": true,
        "doc_values": true
    }
}
```

### ObjectField

Field:

```php
new ObjectField('object', /* ... */);
```

Mapping:

```json
{
    "object": {
        "type": "object",
        "properties": {
            /* ... */
        }
    }
}
```

### TypedField

Field:

```php
new TypedField('typed', 'type', ['type_a' =>  /* ... */, 'type_b' =>  /* ... */]);
```

Mapping:

```json
{
    "typed": {
        "type": "object",
        "properties": {
            "type_a": {
                "type": "object",
                "properties": {
                    /* ... */
                }
            },
            "type_b": {
                "type": "object",
                "properties": {
                    /* ... */
                }
            }
        }
    }
}
```
