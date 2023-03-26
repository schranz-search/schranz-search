<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\RediSearch\Tests;

use Schranz\Search\SEAL\Adapter\RediSearch\RediSearchAdapter;
use Schranz\Search\SEAL\Testing\AbstractAdapterTestCase;

class RediSearchAdapterTest extends AbstractAdapterTestCase
{
    public static function setUpBeforeClass(): void
    {
        $client = ClientHelper::getClient();
        self::$adapter = new RediSearchAdapter($client);

        parent::setUpBeforeClass();
    }
}
