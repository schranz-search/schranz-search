<?php

namespace Schranz\Search\SEAL\Adapter\Elasticsearch\Tests;

use Schranz\Search\SEAL\Adapter\Elasticsearch\ElasticsearchAdapter;
use Schranz\Search\SEAL\Testing\AbstractSearcherTestCase;

class ElasticsearcSearcherTest extends AbstractSearcherTestCase
{
    public static function setUpBeforeClass(): void
    {
        $client = ClientHelper::getClient();
        self::$adapter = new ElasticsearchAdapter($client);

        parent::setUpBeforeClass();
    }
}
