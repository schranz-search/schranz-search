<?php

declare(strict_types=1);

namespace Schranz\Search\Integration\Mezzio\Service;

use Psr\Container\ContainerInterface;
use Schranz\Search\SEAL\Adapter\AdapterFactory;
use Schranz\Search\SEAL\Engine;
use Schranz\Search\SEAL\EngineRegistry;
use Schranz\Search\SEAL\Schema\Loader\PhpFileLoader;

/**
 * @internal
 */
final class EngineRegistryFactory
{
    public function __invoke(ContainerInterface $container): EngineRegistry
    {
        $config = $container->get('config');

        /**
         * @var array{
         *     prefix: string,
         *     schemas: array<string, array{
         *         dir: string,
         *         engine?: string,
         *     }>,
         *     engines: array<string, array{
         *         adapter: string,
         *     }>,
         * } $config
         */
        $config = $config['schranz_search'];

        $prefix = $config['prefix'];

        $engineSchemaDirs = [];
        foreach ($config['schemas'] as $options) {
            $engineSchemaDirs[$options['engine'] ?? 'default'][] = $options['dir'];
        }

        /** @var AdapterFactory $adapterFactory */
        $adapterFactory = $container->get(AdapterFactory::class);

        $engineServices = [];
        foreach ($config['engines'] as $name => $engineConfig) {
            /** @var string $adapterDsn */
            $adapterDsn = $engineConfig['adapter'];
            $dirs = $engineSchemaDirs[$name] ?? [];

            $adapter = $adapterFactory->createAdapter($adapterDsn);
            $loader = new PhpFileLoader($dirs, $prefix);
            $schema = $loader->load();

            $engine = new Engine($adapter, $schema);

            $engineServices[$name] = $engine;
            if ('default' === $name || (!isset($engineServices['default']) && !isset($config['engines']['default']))) {
                $engineServices['default'] = $engine;
            }
        }

        return new EngineRegistry($engineServices);
    }
}
