<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\OpenAIPostgreSQL;

use OpenAI;
use OpenAI\Client;
use Psr\Container\ContainerInterface;
use Schranz\Search\SEAL\Adapter\AdapterFactoryInterface;
use Schranz\Search\SEAL\Adapter\AdapterInterface;

/**
 * @experimental
 */
class OpenAIPostgreSQLAdapterFactory implements AdapterFactoryInterface
{
    public function __construct(
        private readonly ?ContainerInterface $container = null,
    ) {
    }

    public function createAdapter(array $dsn): AdapterInterface
    {
        $openAIClient = $this->createOpenAiClient($dsn);
        $pdoClient = $this->createPdoClient($dsn);

        return new OpenAIPostgreSQLAdapter($openAIClient, $pdoClient);
    }

    /**
     * @internal
     *
     * @param array{
     *     query: array<string, string>,
     * } $dsn
     */
    public function createOpenAiClient(array $dsn): Client
    {
        return OpenAI::client($dsn['query']['openai-api-key'] ?? '', $dsn['query']['openai-organisation'] ?? null);
    }

    /**
     * @internal
     *
     * @param array{
     *     host: string,
     *     port?: int,
     *     user?: string,
     *     pass?: string,
     *     path?: string,
     * } $dsn
     */
    public function createPdoClient(array $dsn): \PDO
    {
        $host = $dsn['host'];
        $port = $dsn['port'] ?? 5432;
        $user = $dsn['user'] ?? '';
        $pass = $dsn['pass'] ?? '';
        $db = $dsn['path'] ?? 'search';

        return new \PDO(
            'pgsql:host=' . $host . ';port=' . $port . ';dbname=' . $db . ';',
            $user,
            $pass,
            [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
        );
    }

    public static function getName(): string
    {
        return 'openai-postgresql';
    }
}
