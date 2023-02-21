<?php

namespace Schranz\Search\SEAL\Adapter\Meilisearch;

use Meilisearch\Client;
use Psr\Container\ContainerInterface;
use Schranz\Search\SEAL\Adapter\AdapterFactoryInterface;
use Schranz\Search\SEAL\Adapter\AdapterInterface;

/**
 * @experimental
 */
class MeilisearchAdapterFactory implements AdapterFactoryInterface
{
    public function __construct(
        private readonly ?ContainerInterface $container = null
    ) {
    }

    public function getAdapter(array $dsn): AdapterInterface
    {
        $client = $this->createClient($dsn);

        return new MeilisearchAdapter($client);
    }

    /**
     * @internal
     *
     * @param array{
     *     host: string,
     *     port: ?int,
     *     user: ?string,
     * } $dsn
     */
    public function createClient(array $dsn): Client
    {
        if (!isset($dsn['host'])) {
            $client = $this->container?->get(Client::class);

            if (!$client instanceof Client) {
                throw new \InvalidArgumentException('Unknown Meilisearch client.');
            }

            return $client;
        }

        $apiKey = $dsn['user'] ?? '';

        $client = new Client(
            $dsn['host'] . ':' . ($dsn['port'] ?? 7700),
            $apiKey
        );

        return $client->build();
    }

    public static function getName(): string
    {
        return 'meilisearch';
    }
}
