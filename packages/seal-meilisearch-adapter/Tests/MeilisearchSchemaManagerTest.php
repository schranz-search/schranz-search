<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\Meilisearch\Tests;

use Schranz\Search\SEAL\Adapter\Meilisearch\MeilisearchSchemaManager;
use Schranz\Search\SEAL\Testing\AbstractSchemaManagerTestCase;

class MeilisearchSchemaManagerTest extends AbstractSchemaManagerTestCase
{
    public static function setUpBeforeClass(): void
    {
        $client = ClientHelper::getClient();
        self::$schemaManager = new MeilisearchSchemaManager($client);

        parent::setUpBeforeClass();
    }
}
