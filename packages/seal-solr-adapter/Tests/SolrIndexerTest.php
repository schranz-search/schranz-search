<?php

namespace Schranz\Search\SEAL\Adapter\Solr\Tests;

use Schranz\Search\SEAL\Adapter\Solr\SolrAdapter;
use Schranz\Search\SEAL\Testing\AbstractIndexerTestCase;

class SolrIndexerTest extends AbstractIndexerTestCase
{
    public static function setUpBeforeClass(): void
    {
        $client = ClientHelper::getClient();
        self::$adapter = new SolrAdapter($client);

        parent::setUpBeforeClass();
    }
}
