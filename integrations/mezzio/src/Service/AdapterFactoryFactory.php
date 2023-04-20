<?php

declare(strict_types=1);

namespace Schranz\Search\Integration\Mezzio\Service;

use Psr\Container\ContainerInterface;
use Schranz\Search\SEAL\Adapter\AdapterFactory;
use Schranz\Search\SEAL\Adapter\AdapterFactoryInterface;

/**
 * @internal
 */
class AdapterFactoryFactory
{
    public function __invoke(ContainerInterface $container): AdapterFactory
    {
        $config = $container->get('config');

        /** @var array<string, string> $registeredFactories */
        $registeredFactories = $config['schranz_search']['adapter_factories'];

        /** @var AdapterFactoryInterface[] $factories */
        $factories = [];
        foreach ($registeredFactories as $name => $service) {
            $factories[$name] = $container->get($service);
        }

        return new AdapterFactory($factories);
    }
}
