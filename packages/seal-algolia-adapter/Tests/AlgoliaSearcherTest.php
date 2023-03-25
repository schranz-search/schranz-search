<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\Algolia\Tests;

use Schranz\Search\SEAL\Adapter\Algolia\AlgoliaAdapter;
use Schranz\Search\SEAL\Testing\AbstractSearcherTestCase;

class AlgoliaSearcherTest extends AbstractSearcherTestCase
{
    public static function setUpBeforeClass(): void
    {
        $client = ClientHelper::getClient();
        self::$adapter = new AlgoliaAdapter($client);

        parent::setUpBeforeClass();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testFindMultipleIndexes(): void
    {
        $this->markTestSkipped('Not supported by Algolia: https://github.com/schranz-search/schranz-search/issues/41');
    }
}
