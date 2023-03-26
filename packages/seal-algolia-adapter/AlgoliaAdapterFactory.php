<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\Algolia;

use Algolia\AlgoliaSearch\SearchClient;
use Psr\Container\ContainerInterface;
use Schranz\Search\SEAL\Adapter\AdapterFactoryInterface;
use Schranz\Search\SEAL\Adapter\AdapterInterface;

/**
 * @experimental
 */
final class AlgoliaAdapterFactory implements AdapterFactoryInterface
{
    public function __construct(
        private readonly ?ContainerInterface $container = null,
    ) {
    }

    public function createAdapter(array $dsn): AdapterInterface
    {
        $client = $this->createClient($dsn);

        return new AlgoliaAdapter($client);
    }

    /**
     * @internal
     *
     * @param array{
     *     host: string,
     *     user?: string,
     *     pass?: string,
     * } $dsn
     */
    public function createClient(array $dsn): SearchClient
    {
        if ('algolia' !== $dsn['host']) {
            $client = $this->container?->get($dsn['host']);

            if (!$client instanceof SearchClient) {
                throw new \InvalidArgumentException('Unknown Algolia client: ' . $dsn['host']);
            }

            return $client;
        }

        $applicationId = $dsn['user'] ?? null;
        $adminApiKey = $dsn['pass'] ?? null;

        if (!$applicationId || !$adminApiKey) {
            $client = $this->container?->has(SearchClient::class) ? $this->container->get(SearchClient::class) : null;

            if (!$client instanceof SearchClient) {
                throw new \InvalidArgumentException(\sprintf(
                    'Unknown Algolia client: Could not find service "%s" or no APPLICATION_ID or ADMIN_API_KEY provided.',
                    SearchClient::class,
                ));
            }

            return $client;
        }

        return SearchClient::create(
            $applicationId,
            $adminApiKey,
        );
    }

    public static function getName(): string
    {
        return 'algolia';
    }
}
