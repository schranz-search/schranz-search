<?php

namespace Schranz\Search\SEAL\Adapter\Memory\Tests;

use Schranz\Search\SEAL\Adapter\Memory\MemoryAdapter;
use Schranz\Search\SEAL\Testing\AbstractIndexerTestCase;

class MemoryIndexerTest extends AbstractIndexerTestCase
{
    public static function setUpBeforeClass(): void
    {
        self::$adapter = new MemoryAdapter();

        parent::setUpBeforeClass();
    }
}
