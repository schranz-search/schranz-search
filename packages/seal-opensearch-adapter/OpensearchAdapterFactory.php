<?php

namespace Schranz\Search\SEAL\Adapter\Opensearch;

use OpenSearch\ClientBuilder;
use OpenSearch\Client;
use Psr\Container\ContainerInterface;
use Schranz\Search\SEAL\Adapter\AdapterFactoryInterface;
use Schranz\Search\SEAL\Adapter\AdapterInterface;

/**
 * @experimental
 */
class OpensearchAdapterFactory implements AdapterFactoryInterface
{
    public function __construct(
        private readonly ?ContainerInterface $container = null
    ) {
    }

    public function getAdapter(array $dsn): AdapterInterface
    {
        $client = $this->createClient($dsn);

        return new OpensearchAdapter($client);
    }

    /**
     * @internal
     *
     * @param array{
     *     host: string,
     *     port: ?int,
     *     user: ?string,
     *     pass: ?string,
     * } $dsn
     */
    public function createClient(array $dsn): Client
    {
        if (!isset($dsn['host'])) {
            $client = $this->container?->get(Client::class);

            if (!$client instanceof Client) {
                throw new \InvalidArgumentException('Unknown Opensearch client.');
            }

            return $client;
        }

        $client = ClientBuilder::create()->setHosts([
            $dsn['host'] . ':' . ($dsn['port'] ?? 9200),
        ]);

        $user = $dsn['user'] ?? '';
        $pass = $dsn['pass'] ?? '';

        if ($user || $pass) {
            $client->setBasicAuthentication($user, $pass);
        }

        return $client->build();
    }

    public static function getName(): string
    {
        return 'opensearch';
    }
}
