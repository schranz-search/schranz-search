<?php

declare(strict_types=1);

namespace Schranz\Search\Integration\Mezzio\Service;

use Psr\Container\ContainerInterface;
use Schranz\Search\SEAL\Adapter\AdapterFactoryInterface;
use Schranz\Search\SEAL\Adapter\Multi\MultiAdapterFactory;
use Schranz\Search\SEAL\Adapter\ReadWrite\ReadWriteAdapterFactory;

/**
 * @internal
 */
final class AdapterFactoryAbstractFactory
{
    public function __invoke(ContainerInterface $container, string $className): AdapterFactoryInterface
    {
        if (
            ReadWriteAdapterFactory::class === $className
            || MultiAdapterFactory::class === $className
        ) {
            return new $className(
                $container,
                'schranz_search.adapter.', // FIXME this currently does not work as the adapter instance is not a service
            );
        }

        return new $className($container);
    }
}
