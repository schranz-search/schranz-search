<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\Multi;

use Psr\Container\ContainerInterface;
use Schranz\Search\SEAL\Adapter\AdapterFactoryInterface;
use Schranz\Search\SEAL\Adapter\AdapterInterface;

/**
 * @experimental
 */
class MultiAdapterFactory implements AdapterFactoryInterface
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly string $prefix = '',
    ) {
    }

    public function createAdapter(array $dsn): AdapterInterface
    {
        $adapters = $this->getAdapters($dsn);

        return new MultiAdapter($adapters);
    }

    /**
     * @internal
     *
     * @param array{
     *     host: string,
     *     query: array<string, string>,
     * } $dsn
     *
     * @return iterable<AdapterInterface>
     */
    public function getAdapters(array $dsn): iterable
    {
        /** @var string[] $adapterNames */
        $adapterNames = $dsn['query']['adapters'] ?? [];

        $adapterNames = \array_merge(\array_filter([$dsn['host']]), $adapterNames);
        foreach ($adapterNames as $adapterName) {
            /** @var AdapterInterface $adapter */
            $adapter = $this->container->get($this->prefix . $adapterName);

            yield $adapterName => $adapter;
        }
    }

    public static function getName(): string
    {
        return 'multi';
    }
}
