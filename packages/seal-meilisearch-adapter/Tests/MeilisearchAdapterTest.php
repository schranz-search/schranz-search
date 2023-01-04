<?php

namespace Schranz\Search\SEAL\Adapter\Meilisearch\Tests;

use Schranz\Search\SEAL\Adapter\Meilisearch\MeilisearchAdapter;
use Schranz\Search\SEAL\Testing\AbstractAdapterTestCase;

class MeilisearchAdapterTest extends AbstractAdapterTestCase
{
    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = ClientHelper::getClient();

        self::$adapter = new MeilisearchAdapter(self::$client);
    }

    protected static function waitForCreateIndex(): void
    {
        usleep((int) ($_ENV['MEILISEARCH_WAIT_TIME'] ?? 100_000));
    }

    protected static function waitForDropIndex(): void
    {
        usleep((int) ($_ENV['MEILISEARCH_WAIT_TIME'] ?? 100_000));
    }

    public static function waitForAddDocuments(): void
    {
        usleep((int) ($_ENV['MEILISEARCH_WAIT_TIME'] ?? 100_000));
    }

    public static function waitForDeleteDocuments(): void
    {
        usleep((int) ($_ENV['MEILISEARCH_WAIT_TIME'] ?? 100_000));
    }
}
