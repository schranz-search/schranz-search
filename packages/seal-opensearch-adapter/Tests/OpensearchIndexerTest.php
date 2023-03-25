<?php

namespace Schranz\Search\SEAL\Adapter\Opensearch\Tests;

use Schranz\Search\SEAL\Adapter\Opensearch\OpensearchAdapter;
use Schranz\Search\SEAL\Testing\AbstractIndexerTestCase;

class OpensearcnIndexerTest extends AbstractIndexerTestCase
{
    public static function setUpBeforeClass(): void
    {
        $client = ClientHelper::getClient();
        self::$adapter = new OpensearchAdapter($client);

        parent::setUpBeforeClass();
    }
}
