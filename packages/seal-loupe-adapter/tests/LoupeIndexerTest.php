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

namespace Schranz\Search\SEAL\Adapter\Loupe\Tests;

use Schranz\Search\SEAL\Adapter\Loupe\LoupeAdapter;
use Schranz\Search\SEAL\Testing\AbstractIndexerTestCase;

class LoupeIndexerTest extends AbstractIndexerTestCase
{
    public static function setUpBeforeClass(): void
    {
        $helper = ClientHelper::getHelper();
        self::$adapter = new LoupeAdapter($helper);

        parent::setUpBeforeClass();
    }
}
