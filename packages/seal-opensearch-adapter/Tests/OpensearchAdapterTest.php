<?php

namespace Schranz\Search\SEAL\Adapter\Opensearch\Tests;

use Schranz\Search\SEAL\Adapter\Opensearch\OpensearchAdapter;
use Schranz\Search\SEAL\Testing\AbstractAdapterTestCase;

class OpensearchAdapterTest extends AbstractAdapterTestCase
{
    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = ClientHelper::getClient();

        self::$adapter = new OpensearchAdapter(self::$client);
    }

    public static function waitForAddDocuments(): void
    {
        usleep((int) ($_ENV['OPENSEARCH_WAIT_TIME'] ?? 1_000_000));
    }

    public static function waitForDeleteDocuments(): void
    {
        usleep((int) ($_ENV['OPENSEARCH_WAIT_TIME'] ?? 1_000_000));
    }
}
