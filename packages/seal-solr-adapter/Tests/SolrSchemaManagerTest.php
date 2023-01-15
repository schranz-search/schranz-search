<?php

namespace Schranz\Search\SEAL\Adapter\Solr\Tests;

use Schranz\Search\SEAL\Adapter\Solr\SolrSchemaManager;
use Schranz\Search\SEAL\Testing\AbstractSchemaManagerTestCase;

class SolrSchemaManagerTest extends AbstractSchemaManagerTestCase
{
    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = ClientHelper::getClient();

        self::$schemaManager = new SolrSchemaManager(self::$client);
    }
}
