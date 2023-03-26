<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\RediSearch\Tests;

use Schranz\Search\SEAL\Adapter\RediSearch\RediSearchAdapter;
use Schranz\Search\SEAL\Testing\AbstractIndexerTestCase;

class RediSearchIndexerTest extends AbstractIndexerTestCase
{
    public static function setUpBeforeClass(): void
    {
        $client = ClientHelper::getClient();
        self::$adapter = new RediSearchAdapter($client);

        parent::setUpBeforeClass();
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testSaveDeleteIdentifierCondition(): void
    {
        $this->markTestSkipped('Not supported by RediSearch: https://github.com/schranz-search/schranz-search/issues/92');
    }
}
