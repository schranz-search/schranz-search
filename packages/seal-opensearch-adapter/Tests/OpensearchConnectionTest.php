<?php

namespace Schranz\Search\SEAL\Adapter\Opensearch\Tests;

use Schranz\Search\SEAL\Adapter\Opensearch\OpensearchConnection;
use Schranz\Search\SEAL\Adapter\Opensearch\OpensearchSchemaManager;
use Schranz\Search\SEAL\Testing\AbstractConnectionTestCase;

class OpensearchConnectionTest extends AbstractConnectionTestCase
{
    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = ClientHelper::getClient();

        self::$connection = new OpensearchConnection(self::$client);
        self::$schemaManager = new OpensearchSchemaManager(self::$client);

        parent::setUpBeforeClass();
    }

    public static function waitForAddDocuments(): void
    {
        usleep((int) ($_ENV['OPENSEARCH_WAIT_TIME'] ?? 100_000));
    }

    public static function waitForDeleteDocuments(): void
    {
        usleep((int) ($_ENV['OPENSEARCH_WAIT_TIME'] ?? 100_000));
    }
}
