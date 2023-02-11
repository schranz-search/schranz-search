<?php

namespace Schranz\Search\SEAL\Adapter\RediSearch\Tests;

use Schranz\Search\SEAL\Adapter\RediSearch\RediSearchAdapter;
use Schranz\Search\SEAL\Testing\AbstractAdapterTestCase;

class RediSearchAdapterTest extends AbstractAdapterTestCase
{
    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = ClientHelper::getClient();

        self::$adapter = new RediSearchAdapter(self::$client);
    }
}
