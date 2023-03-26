<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\Typesense\Tests;

use Schranz\Search\SEAL\Adapter\Typesense\TypesenseAdapter;
use Schranz\Search\SEAL\Testing\AbstractAdapterTestCase;

class TypesenseAdapterTest extends AbstractAdapterTestCase
{
    public static function setUpBeforeClass(): void
    {
        $client = ClientHelper::getClient();
        self::$adapter = new TypesenseAdapter($client);

        parent::setUpBeforeClass();
    }
}
