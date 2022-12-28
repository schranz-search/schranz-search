<?php

namespace Schranz\Search\SEAL\Adapter;

interface AdapterInterface
{
    public function getSchemaManager(): SchemaManagerInterface;

    public function getConnection(): ConnectionInterface;
}
