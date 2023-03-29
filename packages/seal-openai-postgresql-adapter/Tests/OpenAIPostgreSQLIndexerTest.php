<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\OpenAIPostgreSQL\Tests;

use Schranz\Search\SEAL\Adapter\OpenAIPostgreSQL\OpenAIPostgreSQLAdapter;
use Schranz\Search\SEAL\Testing\AbstractIndexerTestCase;

class OpenAIPostgreSQLIndexerTest extends AbstractIndexerTestCase
{
    public static function setUpBeforeClass(): void
    {
        $openAiClient = ClientHelper::getOpenAiClient();
        $pdoClient = ClientHelper::getPdoClient();
        self::$adapter = new OpenAIPostgreSQLAdapter($openAiClient, $pdoClient);

        parent::setUpBeforeClass();
    }
}
