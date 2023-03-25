<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\Algolia\Tests;

use Schranz\Search\SEAL\Adapter\Algolia\AlgoliaSchemaManager;
use Schranz\Search\SEAL\Testing\AbstractSchemaManagerTestCase;

class AlgoliaSchemaManagerTest extends AbstractSchemaManagerTestCase
{
    public static function setUpBeforeClass(): void
    {
        $client = ClientHelper::getClient();
        self::$schemaManager = new AlgoliaSchemaManager($client);

        parent::setUpBeforeClass();
    }
}
