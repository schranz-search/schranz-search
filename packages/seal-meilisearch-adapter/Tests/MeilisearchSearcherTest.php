<?php

namespace Schranz\Search\SEAL\Adapter\Meilisearch\Tests;

use Schranz\Search\SEAL\Adapter\Meilisearch\MeilisearchAdapter;
use Schranz\Search\SEAL\Testing\AbstractSearcherTestCase;

class MeilisearchSearcherTest extends AbstractSearcherTestCase
{
    public static function setUpBeforeClass(): void
    {
        $client = ClientHelper::getClient();
        self::$adapter = new MeilisearchAdapter($client);

        parent::setUpBeforeClass();
    }

    public function testFindMultipleIndexes(): void
    {
        $this->markTestSkipped('Not supported by Meilisearch: https://github.com/schranz-search/schranz-search/issues/28');
    }
}
