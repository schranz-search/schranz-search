# Schranz Search

Monorepository for the search abstraction over different search engine.

### SEAL

What `doctrine/dbal` is for `doctrine`, the `schranz-search/SEAL` is for `schranz-search` package.
It provides a common interface to interact with different search engines.

Read more about it in the [README.md](src/SEAL/README.md) of the package.

![duffy-duck-investigating](https://user-images.githubusercontent.com/1698337/209232131-8b0a3dcf-8500-45ed-bcc2-1b97a25b1e15.gif)

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
 - [Toshi](#toshi)
 - [Quickwit](#quickwit)
 - [nrtSearch](#nrtsearch)
 - [MongoDB Atlas](#mongodb-atlas)

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

### Toshi

A full-text search engine in rust. Toshi strives to be to Elasticsearch what [Tantivy Server](https://github.com/quickwit-oss/tantivy) is to Lucene:

 - Server: [Toshi Server](https://github.com/toshi-search/Toshi)
 - PHP Client: No client available only API based

### Quickwit

Describe itself as a cloud-native search engine for log management & analytics written in Rust. It is designed to be very cost-effective, easy to operate, and scale to petabytes.

 - Server: [Quickwit Server](https://github.com/quickwit-oss/quickwit)
 - PHP Client: No client available only API based

### nrtSearch

Describe itself as a high performance gRPC server, with optional REST APIs on top of Apache Lucene version 8.x source, exposing Lucene's core functionality over a simple gRPC based API.:

 - Server: [nrtSearch Server](https://github.com/Yelp/nrtsearch)
 - PHP Client: No client available only API based

### MongoDB Atlas

None open source search engine from MongoDB. It is a cloud based search engine.

 - Server: [MongoDB Atlas](https://www.mongodb.com/atlas/search)
 - PHP Client: [MongoDB Atlas PHP Client](https://www.mongodb.com/docs/drivers/php/#connect-to-mongodb-atlas)

## Similar Projects

Following projects in the past target similar problem:

 - [https://github.com/nresni/Ariadne](https://github.com/nresni/Ariadne) (Solr, Elasticsearch, Zendsearch: outdated 12 years ago)
 - [https://github.com/massiveart/MassiveSearchBundle](https://github.com/massiveart/MassiveSearchBundle) (ZendSearch, Elasticsearch)
