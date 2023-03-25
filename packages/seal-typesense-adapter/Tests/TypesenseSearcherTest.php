<?php

namespace Schranz\Search\SEAL\Adapter\Typesense\Tests;

use Schranz\Search\SEAL\Adapter\Typesense\TypesenseAdapter;
use Schranz\Search\SEAL\Testing\AbstractSearcherTestCase;

class TypesenseSearcherTest extends AbstractSearcherTestCase
{
    public static function setUpBeforeClass(): void
    {
        $client = ClientHelper::getClient();
        self::$adapter = new TypesenseAdapter($client);

        parent::setUpBeforeClass();
    }

    public function testFindMultipleIndexes(): void
    {
        $this->markTestSkipped('Not supported by Typesense: https://github.com/schranz-search/schranz-search/issues/98');
    }
}
