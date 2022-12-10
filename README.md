# Schranz Search

Monorepository for the search abstraction over different search engine.

### SEAL

What `doctrine/dbal` is for `doctrine`, the `schranz-search/SEAL` is for `schranz-search` package.
It provides a common interface to interact with different search engines.

Read more about it in the [README.md](src/SEAL/README.md) of the package.

## Research

At current state collect here different search engines which are around and could be interesting:

 - [Elasticsearch](#elasticsearch)
 - [Opensearch](#opensearch)
 - [RediSearch](#redisearch)
 - [Zinc Labs](#zinc-labs)
 - [Typesense](#typesense)
 - [Algolia](#algolia)
 - [ZendSearch](#zendsearch)
 - [TnTSearch](#tntsearch)
 - [MeiliSearch](#meilisearch)
 - [Solr](#solr)
 - [Sonic](#sonic)
 - [Vespa](#vespa)

### Elasticsearch
 
Widely used search based on Java.

 - Server: [Elasticsearch Server](https://github.com/elastic/elasticsearch)
 - PHP Client: [Elasticsearch PHP](https://github.com/elastic/elasticsearch-php)

### Opensearch
 
Fork of Elasticsearch also written in Java.

 - Server: [Opensearch Server](https://github.com/opensearch-project/OpenSearch)
 - PHP Client: [Opensearch PHP](https://github.com/opensearch-project/opensearch-php)

### RediSearch
 
A search out of the house of the redis labs.

 - Server: [RediSearch Server]([https://github.com/opensearch-project/OpenSearch](https://github.com/RediSearch/RediSearch))
 - PHP Client: [RediSearch PHP](https://github.com/MacFJA/php-redisearch)

### Zinc Labs

Zinc search describes itself as a lightweight alternative to Elasticsearch written in GoLang.

 - Server: [Zinclabs Server](https://github.com/zinclabs/zinc)
 - PHP Client: No PHP SDK currently: [https://github.com/zinclabs/zinc/issues/12](https://github.com/zinclabs/zinc/issues/12)

### Typesense

Describes itself as a alternative to Algolia and Elasticsearch written in C++.

 - Server: [Typesense Server](https://github.com/typesense/typesense)
 - PHP Client: [Typesense PHP](https://github.com/typesense/typesense-php)

### Algolia

Is a search as SaaS provided via Rest APIs and SDKs:

 - Server: No server only Saas [https://www.algolia.com/](https://www.algolia.com/)
 - PHP Client: [Algolia PHP](https://github.com/algolia/algoliasearch-client-php)

### ZendSearch

A complete in PHP written implementation of the Lucene index. Not longer maintained:

 - Implementation: [Zendsearch Implementation](https://github.com/handcraftedinthealps/zendsearch)

### TnTSearch

Another implementation of a Search index written in PHP. Not based on Lucene.

 - Implementation: [TntSearch Implementation](https://github.com/teamtnt/tntsearch)

### MeiliSearch

A search engine written in Rust:

 - Server: [MeiliSearch Server](https://github.com/meilisearch/meilisearch)
 - PHP Client: [MeiliSearch PHP](https://github.com/meilisearch/meilisearch-php)

### Solr

A search engine under the Apache Project based on Lucene written in Java:

 - Server: [Solr Server](https://github.com/apache/solr)
 - PHP Client: [Solarium PHP](https://github.com/solariumphp/solarium) seems to be a well maintained Client

### Sonic

Describe itself as lightweight & schema-less search backend, an alternative to Elasticsearch that runs on a few MBs of RAM. 

 - Server: [Sonic Server](https://github.com/valeriansaliou/sonic)
 - PHP Client: [Unoffical PHP Sonic](https://github.com/php-sonic/php-sonic) looks outdated and not well maintained

### Vespa

Describe itself as the open big data serving engine - Store, search, organize and make machine-learned inferences over big data at serving time.

 - Server: [Vespa Server](https://github.com/vespa-engine/vespa)
https://github.com/vespa-engine/vespa
 - PHP Client: No client available only API based
