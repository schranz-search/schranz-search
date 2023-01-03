<?php

namespace Schranz\Search\SEAL\Adapter\Memory\Tests;

use Schranz\Search\SEAL\Adapter\Elasticsearch\Tests\ClientHelper;
use Schranz\Search\SEAL\Adapter\Elasticsearch\ElasticsearchAdapter;
use Schranz\Search\SEAL\Testing\AbstractAdapterTestCase;

class ElasticsearchAdapterTest extends AbstractAdapterTestCase
{
    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = ClientHelper::getClient();

        self::$adapter = new ElasticsearchAdapter(self::$client);
    }
}
