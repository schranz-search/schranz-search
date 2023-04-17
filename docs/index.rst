Welcome to SEAL's documentation!
================================

Introduction
------------

**SEAL** stands for: **S** earch **E** ngine **A** bstraction **L** ayer

The SEAL project is a PHP library designed to simplify the process of interacting
with different search engines. It provides a straightforward interface that enables users
to communicate with various search engines, including:

- ``Meilisearch``
- ``Opensearch``
- ``Elasticsearch``
- ``Algolia``
- ``Redisearch``
- ``Solr``
- ``Typesense``

It also provides integration packages for the ``Laravel``, ``Symfony`` and ``Spiral`` PHP frameworks.

It is worth noting that the project draws inspiration from the
``Doctrine`` and ``Flysystem`` projects. These two projects have been a great inspiration
in the development of SEAL, as they provide excellent examples of how to create consistent
and user-friendly APIs for complex systems.

Contents
--------

.. toctree::
    :maxdepth: 1

    getting_started/index
    schema/index
    indexing/index
    search_and_filters/index
    cookbooks/index

..
  * :ref:`search`

-----------

Packages
--------

Full list of packages provided by the SEAL project:

- ``schranz-search/seal`` - The core package of the SEAL project.
- ``schranz-search/seal-algolia-adapter`` - Adapter for the Algolia search service.
- ``schranz-search/seal-elasticsearch-adapter`` - Adapter for the Elasticsearch search service.
- ``schranz-search/seal-opensearch-adapter`` - Adapter for the Opensearch search service.
- ``schranz-search/seal-meilisearch-adapter`` - Adapter for the Meilisearch search service.
- ``schranz-search/seal-redisearch-adapter`` - Adapter for the Redisearch search service.
- ``schranz-search/seal-solr-adapter`` - Adapter for the Solr search service.
- ``schranz-search/seal-typesense-adapter`` - Adapter for the Typesense search service.
- ``schranz-search/seal-read-write-adapter`` - Adapter to split read and write operations.
- ``schranz-search/seal-multi-adapter`` - Adapter to write into multiple search services.
- ``schranz-search/laravel-package`` - Integrates SEAL into the Laravel framework.
- ``schranz-search/symfony-bundle`` - Integrates SEAL into the Symfony framework.
- ``schranz-search/spiral-bridge`` - Integrates SEAL into the Spiral framework.

Have also a look at the following tags:

- `https://packagist.org/search/?tags=seal-adapter <https://packagist.org/search/?tags=seal-adapter>`_
- `https://github.com/topics/seal-php-adapter <https://github.com/topics/seal-php-adapter>`_
