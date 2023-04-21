<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\OpenAIPostgreSQL\Tests;

use OpenAI;
use OpenAI\Client;

final class ClientHelper
{
    private static ?Client $openAiClient = null;

    private static ?\PDO $pdoClient = null;

    public static function getOpenAiClient(): Client
    {
        if (!self::$openAiClient instanceof Client) {
            self::$openAiClient = OpenAI::client($_ENV['OPEN_AI_API_KEY'] ?? '', $_ENV['OPEN_AI_ORGANISATION'] ?? '');
        }

        return self::$openAiClient;
    }

    public static function getPdoClient(): \PDO
    {
        if (!self::$pdoClient instanceof \PDO) {
            $host = $_ENV['POSTGRES_HOST'] = '127.0.0.1';
            $port = $_ENV['POSTGRES_PORT'] = 5432;
            $db = $_ENV['POSTGRES_DB'] = 'app';
            $user = $_ENV['POSTGRES_USER'] = 'app';
            $pass = $_ENV['POSTGRES_PASSWORD'] = '!ChangeMe!';

            self::$pdoClient = new \PDO(
                'pgsql:host=' . $host . ';port=' . $port . ';dbname=' . $db . ';',
                $user,
                $pass,
                [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
            );
        }

        return self::$pdoClient;
    }
}
