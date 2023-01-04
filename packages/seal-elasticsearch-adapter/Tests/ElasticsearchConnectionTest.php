<?php

namespace Schranz\Search\SEAL\Adapter\Elasticsearch\Tests;

use Schranz\Search\SEAL\Adapter\Elasticsearch\ElasticsearchConnection;
use Schranz\Search\SEAL\Adapter\Elasticsearch\ElasticsearchSchemaManager;
use Schranz\Search\SEAL\Testing\AbstractConnectionTestCase;

class ElasticsearchConnectionTest extends AbstractConnectionTestCase
{
    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = ClientHelper::getClient();

        self::$connection = new ElasticsearchConnection(self::$client);
        self::$schemaManager = new ElasticsearchSchemaManager(self::$client);

        parent::setUpBeforeClass();
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
