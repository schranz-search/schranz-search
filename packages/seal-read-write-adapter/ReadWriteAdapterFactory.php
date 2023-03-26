<?php

declare(strict_types=1);

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
        private readonly string $prefix = '',
    ) {
    }

    public function createAdapter(array $dsn): AdapterInterface
    {
        if (!isset($dsn['query']['write'])) {
            throw new \InvalidArgumentException('The "write" parameter is missing in the DSN for "read-write" Adapter Factory.');
        }

        /** @var AdapterInterface $readAdapter */
        $readAdapter = $this->container->get($this->prefix . $dsn['host']);
        /** @var AdapterInterface $writeAdapter */
        $writeAdapter = $this->container->get($this->prefix . $dsn['query']['write']);

        return new ReadWriteAdapter($readAdapter, $writeAdapter);
    }

    public static function getName(): string
    {
        return 'read-write';
    }
}
