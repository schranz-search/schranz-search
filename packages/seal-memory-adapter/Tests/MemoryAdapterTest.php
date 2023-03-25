<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\Memory\Tests;

use Schranz\Search\SEAL\Adapter\Memory\MemoryAdapter;
use Schranz\Search\SEAL\Testing\AbstractAdapterTestCase;

class MemoryAdapterTest extends AbstractAdapterTestCase
{
    public static function setUpBeforeClass(): void
    {
        self::$adapter = new MemoryAdapter();

        parent::setUpBeforeClass();
    }
}
