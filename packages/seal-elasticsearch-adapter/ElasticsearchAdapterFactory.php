<?php

namespace Schranz\Search\SEAL\Adapter\Elasticsearch;

use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\ClientInterface;
use Psr\Container\ContainerInterface;
use Schranz\Search\SEAL\Adapter\AdapterFactoryInterface;
use Schranz\Search\SEAL\Adapter\AdapterInterface;

/**
 * @experimental
 */
class ElasticsearchAdapterFactory implements AdapterFactoryInterface
{
    public function __construct(
        private readonly ?ContainerInterface $container = null
    ) {
    }

    public function createAdapter(array $dsn): AdapterInterface
    {
        $client = $this->createClient($dsn);

        return new ElasticsearchAdapter($client);
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
    public function createClient(array $dsn): ClientInterface
    {
        if (!isset($dsn['host'])) {
            $client = $this->container?->get(ClientInterface::class);

            if (!$client instanceof ClientInterface) {
                throw new \InvalidArgumentException('Unknown Elasticsearch client.');
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
        return 'elasticsearch';
    }
}
