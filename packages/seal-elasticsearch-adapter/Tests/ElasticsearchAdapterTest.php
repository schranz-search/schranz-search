<?php

namespace Schranz\Search\SEAL\Adapter\Elasticsearch\Tests;

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

    public static function waitForAddDocuments(): void
    {
        usleep((int) ($_ENV['ELASTICSEARCH_WAIT_TIME'] ?? 100_000));
    }

    public static function waitForDeleteDocuments(): void
    {
        usleep((int) ($_ENV['ELASTICSEARCH_WAIT_TIME'] ?? 100_000));
    }
}
