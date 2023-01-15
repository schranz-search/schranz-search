<?php

namespace Schranz\Search\SEAL\Adapter\Zinc\Tests;

use Schranz\Search\SEAL\Adapter\Zinc\ZincAdapter;
use Schranz\Search\SEAL\Testing\AbstractAdapterTestCase;

class ZincAdapterTest extends AbstractAdapterTestCase
{
    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = ClientHelper::getClient();

        self::$adapter = new ZincAdapter(self::$client);
    }
}
