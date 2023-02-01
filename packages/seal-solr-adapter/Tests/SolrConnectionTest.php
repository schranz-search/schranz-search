<?php

namespace Schranz\Search\SEAL\Adapter\Solr\Tests;

use Schranz\Search\SEAL\Adapter\Solr\SolrConnection;
use Schranz\Search\SEAL\Adapter\Solr\SolrSchemaManager;
use Schranz\Search\SEAL\Testing\AbstractConnectionTestCase;

class SolrConnectionTest extends AbstractConnectionTestCase
{
    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = ClientHelper::getClient();

        self::$connection = new SolrConnection(self::$client);
        self::$schemaManager = new SolrSchemaManager(self::$client);

        parent::setUpBeforeClass();
    }

    public function testFindMultipleIndexes(): void
    {
        $this->markTestSkipped('Not supported by Solr: TODO create issue');
    }
}
