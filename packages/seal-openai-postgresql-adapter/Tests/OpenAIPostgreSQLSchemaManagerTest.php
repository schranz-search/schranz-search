<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\OpenAIPostgreSQL\Tests;

use Schranz\Search\SEAL\Adapter\OpenAIPostgreSQL\OpenAIPostgreSQLSchemaManager;
use Schranz\Search\SEAL\Testing\AbstractSchemaManagerTestCase;

class OpenAIPostgreSQLSchemaManagerTest extends AbstractSchemaManagerTestCase
{
    public static function setUpBeforeClass(): void
    {
        $openAiClient = ClientHelper::getOpenAiClient();
        $pdoClient = ClientHelper::getPdoClient();
        self::$schemaManager = new OpenAIPostgreSQLSchemaManager($openAiClient, $pdoClient);

        parent::setUpBeforeClass();
    }
}
