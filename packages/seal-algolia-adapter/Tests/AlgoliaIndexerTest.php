<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\Algolia\Tests;

use Schranz\Search\SEAL\Adapter\Algolia\AlgoliaAdapter;
use Schranz\Search\SEAL\Testing\AbstractIndexerTestCase;

class AlgoliaIndexerTest extends AbstractIndexerTestCase
{
    public static function setUpBeforeClass(): void
    {
        $client = ClientHelper::getClient();
        self::$adapter = new AlgoliaAdapter($client);

        parent::setUpBeforeClass();
    }
}
