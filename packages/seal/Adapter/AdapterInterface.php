<?php

namespace Schranz\Search\SEAL\Adapter;

interface AdapterInterface
{
    public function getSchemaManager(): SchemaManagerInterface;

    public function getSearcher(): SearcherInterface;

    public function getIndexer(): IndexerInterface;
}
