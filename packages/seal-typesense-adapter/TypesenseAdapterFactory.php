<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\Typesense;

use Http\Discovery\HttpClientDiscovery;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface as HttpClientInterface;
use Schranz\Search\SEAL\Adapter\AdapterFactoryInterface;
use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Typesense\Client;

/**
 * @experimental
 */
class TypesenseAdapterFactory implements AdapterFactoryInterface
{
    public function __construct(
        private readonly ?ContainerInterface $container = null,
    ) {
    }

    public function createAdapter(array $dsn): AdapterInterface
    {
        $client = $this->createClient($dsn);

        return new TypesenseAdapter($client);
    }

    /**
     * @internal
     *
     * @param array{
     *     host: string,
     *     port?: int,
     *     user?: string,
     * } $dsn
     */
    public function createClient(array $dsn): Client
    {
        if ('' === $dsn['host']) {
            $client = $this->container?->get(Client::class);

            if (!$client instanceof Client) {
                throw new \InvalidArgumentException('Unknown Typesense client.');
            }

            return $client;
        }

        return new Client(
            [
                'api_key' => $dsn['user'] ?? null,
                'nodes' => [
                    [
                        'host' => $dsn['host'],
                        'port' => $dsn['port'] ?? 8108,
                        'protocol' => 'http',
                    ],
                ],
                'client' => $this->createClientClient(),
            ],
        );
    }

    private function createClientClient(): HttpClientInterface
    {
        if ($this->container?->has(HttpClientInterface::class)) {
            /** @var HttpClientInterface */
            return $this->container->get(HttpClientInterface::class);
        }

        return HttpClientDiscovery::find();
    }

    public static function getName(): string
    {
        return 'typesense';
    }
}
