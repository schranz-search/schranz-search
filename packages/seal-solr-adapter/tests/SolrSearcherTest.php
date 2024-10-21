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

namespace Schranz\Search\SEAL\Adapter\Solr\Tests;

use Schranz\Search\SEAL\Adapter\Solr\SolrAdapter;
use Schranz\Search\SEAL\Testing\AbstractSearcherTestCase;

class SolrSearcherTest extends AbstractSearcherTestCase
{
    public static function setUpBeforeClass(): void
    {
        $client = ClientHelper::getClient();
        self::$adapter = new SolrAdapter($client);

        parent::setUpBeforeClass();
    }
}
