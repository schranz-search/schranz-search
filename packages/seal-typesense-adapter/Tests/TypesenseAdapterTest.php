<?php

namespace Schranz\Search\SEAL\Adapter\Typesense\Tests;

use Schranz\Search\SEAL\Adapter\Typesense\TypesenseAdapter;
use Schranz\Search\SEAL\Testing\AbstractAdapterTestCase;

class TypesenseAdapterTest extends AbstractAdapterTestCase
{
    private static $client;

    public static function setUpBeforeClass(): void
    {
        self::$client = ClientHelper::getClient();

        self::$adapter = new TypesenseAdapter(self::$client);
    }
}
