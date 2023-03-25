<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter;

interface AdapterInterface
{
    public function getSchemaManager(): SchemaManagerInterface;

    public function getIndexer(): IndexerInterface;

    public function getSearcher(): SearcherInterface;
}
