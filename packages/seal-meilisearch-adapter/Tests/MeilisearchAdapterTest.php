<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\Meilisearch\Tests;

use Schranz\Search\SEAL\Adapter\Meilisearch\MeilisearchAdapter;
use Schranz\Search\SEAL\Testing\AbstractAdapterTestCase;

class MeilisearchAdapterTest extends AbstractAdapterTestCase
{
    public static function setUpBeforeClass(): void
    {
        $client = ClientHelper::getClient();
        self::$adapter = new MeilisearchAdapter($client);

        parent::setUpBeforeClass();
    }
}
