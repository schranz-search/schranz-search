<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\Algolia\Tests;

use Schranz\Search\SEAL\Adapter\Algolia\AlgoliaAdapter;
use Schranz\Search\SEAL\Testing\AbstractAdapterTestCase;

class AlgoliaAdapterTest extends AbstractAdapterTestCase
{
    public static function setUpBeforeClass(): void
    {
        $client = ClientHelper::getClient();
        self::$adapter = new AlgoliaAdapter($client);

        parent::setUpBeforeClass();
    }
}
