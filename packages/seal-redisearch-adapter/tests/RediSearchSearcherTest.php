<?php

declare(strict_types=1);

/*
 * This file is part of the Schranz Search package.
 *
 * (c) Alexander Schranz <alexander@sulu.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Schranz\Search\SEAL\Adapter\RediSearch\Tests;

use Schranz\Search\SEAL\Adapter\RediSearch\RediSearchAdapter;
use Schranz\Search\SEAL\Testing\AbstractSearcherTestCase;

class RediSearchSearcherTest extends AbstractSearcherTestCase
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
    public function testFindMultipleIndexes(): void
    {
        $this->markTestSkipped('Not supported by RediSearch: https://github.com/schranz-search/schranz-search/issues/93');
    }
}
