<?php

namespace Schranz\Search\SEAL\Adapter\Elasticsearch\Tests;

use Schranz\Search\SEAL\Adapter\Elasticsearch\ElasticsearchSchemaManager;
use Schranz\Search\SEAL\Testing\AbstractSchemaManagerTestCase;

class ElasticsearchSchemaManagerTest extends AbstractSchemaManagerTestCase
{
    public static function setUpBeforeClass(): void
    {
        self::$schemaManager = new ElasticsearchSchemaManager(ClientHelper::getClient());
    }
}
