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

    protected function waitForCreateIndex(): void
    {
        usleep(100_000); // wait 100ms for async index creation
    }

    public function waitForAddDocuments(): void
    {
        usleep(100_000); // wait 100ms for async document add
    }

    public function waitForDeleteDocuments(): void
    {
        usleep(100_000); // wait 100ms for async document deletion
    }
}
