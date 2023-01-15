<?php

namespace Schranz\Search\SEAL\Adapter\Solr\Tests;

use Schranz\Search\SEAL\Adapter\Solr\SolrAdapter;
use Schranz\Search\SEAL\Testing\AbstractAdapterTestCase;

class SolrAdapterTest extends AbstractAdapterTestCase
{
    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = ClientHelper::getClient();

        self::$adapter = new SolrAdapter(self::$client);
    }
}
