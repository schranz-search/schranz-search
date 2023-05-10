<?php

declare(strict_types=1);

/*
 * This file is part of the Schranz Search package.
 *
 * (c) Alexander Schranz <alexander@sulu.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Schranz\Search\Integration\Mezzio\Service;

use Psr\Container\ContainerInterface;
use Schranz\Search\SEAL\Adapter\AdapterFactory;
use Schranz\Search\SEAL\Adapter\AdapterFactoryInterface;
use Schranz\Search\SEAL\Adapter\Multi\MultiAdapterFactory;
use Schranz\Search\SEAL\Adapter\ReadWrite\ReadWriteAdapterFactory;
use Schranz\Search\SEAL\Engine;
use Schranz\Search\SEAL\EngineInterface;
use Schranz\Search\SEAL\EngineRegistry;
use Schranz\Search\SEAL\Schema\Loader\PhpFileLoader;

/**
 * @internal
 */
final class SealContainerFactory
{
    public function __invoke(ContainerInterface $container): SealContainer
    {
        /** @var array{schranz_search: mixed[]} $config */
        $config = $container->get('config');

        /**
         * @var array{
         *     index_name_prefix: string,
         *     schemas: array<string, array{
         *         dir: string,
         *         engine?: string,
         *     }>,
         *     engines: array<string, array{
         *         adapter: string,
         *     }>,
         *     adapter_factories: array<class-string, class-string<AdapterFactoryInterface>>,
         *     reindex_providers: string[],
         * } $config
         */
        $config = $config['schranz_search'];

        $indexNamePrefix = $config['index_name_prefix'];
        $adapterFactoriesConfig = $config['adapter_factories'];

        $engineSchemaDirs = [];
        foreach ($config['schemas'] as $options) {
            $engineSchemaDirs[$options['engine'] ?? 'default'][] = $options['dir'];
        }

        $sealContainer = new SealContainer($container);

        $adapterFactories = [];
        foreach ($adapterFactoriesConfig as $name => $adapterFactoryClass) {
            if (
                ReadWriteAdapterFactory::class === $adapterFactoryClass
                || MultiAdapterFactory::class === $adapterFactoryClass
            ) {
                $adapterFactories[$name] = new $adapterFactoryClass(
                    $sealContainer,
                    'schranz_search.adapter.',
                );

                continue;
            }

            $adapterFactories[$name] = new $adapterFactoryClass($sealContainer);
        }

        $adapterFactory = new AdapterFactory($adapterFactories);

        $sealContainer->set(AdapterFactory::class, $adapterFactory);

        $engineServices = [];
        foreach ($config['engines'] as $name => $engineConfig) {
            $adapterServiceId = 'schranz_search.adapter.' . $name;
            $engineServiceId = 'schranz_search.engine.' . $name;
            $schemaLoaderServiceId = 'schranz_search.schema_loader.' . $name;
            $schemaId = 'schranz_search.schema.' . $name;

            /** @var string $adapterDsn */
            $adapterDsn = $engineConfig['adapter'];
            $dirs = $engineSchemaDirs[$name] ?? [];

            $adapter = $adapterFactory->createAdapter($adapterDsn);
            $loader = new PhpFileLoader($dirs, $indexNamePrefix);
            $schema = $loader->load();

            $engine = new Engine($adapter, $schema);

            $engineServices[$name] = $engine;
            if ('default' === $name || (!isset($engineServices['default']) && !isset($config['engines']['default']))) {
                $engineServices['default'] = $engine;
                $sealContainer->set(EngineInterface::class, $engine);
            }

            $sealContainer->set($adapterServiceId, $adapter);
            $sealContainer->set($engineServiceId, $engine);
            $sealContainer->set($schemaLoaderServiceId, $loader);
            $sealContainer->set($schemaId, $schema);
        }

        $sealContainer->set(EngineRegistry::class, new EngineRegistry($engineServices));

        return $sealContainer;
    }
}
