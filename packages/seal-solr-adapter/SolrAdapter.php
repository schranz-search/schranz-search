<?php

namespace Schranz\Search\SEAL\Adapter\Solr;

use Solarium\Client;
use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\Adapter\ConnectionInterface;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;

final class SolrAdapter implements AdapterInterface
{
    private readonly ConnectionInterface $connection;

    private readonly SchemaManagerInterface $schemaManager;

    public function __construct(
        private readonly Client $client,
        ?ConnectionInterface $connection = null,
        ?SchemaManagerInterface $schemaManager = null,
    ) {
        $this->connection = $connection ?? new SolrConnection($client);
        $this->schemaManager = $schemaManager ?? new SolrSchemaManager($client);
    }

    public function getSchemaManager(): SchemaManagerInterface
    {
        return $this->schemaManager;
    }

    public function getConnection(): ConnectionInterface
    {
        return $this->connection;
    }
}
