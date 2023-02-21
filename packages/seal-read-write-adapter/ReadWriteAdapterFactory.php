<?php

namespace Schranz\Search\SEAL\Adapter\ReadWrite;

use Psr\Container\ContainerInterface;
use Schranz\Search\SEAL\Adapter\AdapterFactoryInterface;
use Schranz\Search\SEAL\Adapter\AdapterInterface;

/**
 * @experimental
 */
class ReadWriteAdapterFactory implements AdapterFactoryInterface
{
    public function __construct(
        private readonly ContainerInterface $container,
    ) {
    }

    public function getAdapter(array $dsn): AdapterInterface
    {
        $readAdapter = $this->container->get($dsn['host']);
        $writeAdapter = $this->container->get($dsn['query']['write']);

        return new ReadWriteAdapter($readAdapter, $writeAdapter);
    }

    public static function getName(): string
    {
        return 'read-write';
    }
}
