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

namespace Schranz\Search\Integration\Spiral\Bootloader;

use Schranz\Search\Integration\Spiral\Config\SearchConfig;
use Schranz\Search\Integration\Spiral\Console\IndexCreateCommand;
use Schranz\Search\Integration\Spiral\Console\IndexDropCommand;
use Schranz\Search\SEAL\Adapter\AdapterFactory;
use Schranz\Search\SEAL\Adapter\AdapterFactoryInterface;
use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\Adapter\Algolia\AlgoliaAdapterFactory;
use Schranz\Search\SEAL\Adapter\Elasticsearch\ElasticsearchAdapterFactory;
use Schranz\Search\SEAL\Adapter\Meilisearch\MeilisearchAdapterFactory;
use Schranz\Search\SEAL\Adapter\Memory\MemoryAdapterFactory;
use Schranz\Search\SEAL\Adapter\Multi\MultiAdapterFactory;
use Schranz\Search\SEAL\Adapter\Opensearch\OpensearchAdapterFactory;
use Schranz\Search\SEAL\Adapter\ReadWrite\ReadWriteAdapterFactory;
use Schranz\Search\SEAL\Adapter\RediSearch\RediSearchAdapterFactory;
use Schranz\Search\SEAL\Adapter\Solr\SolrAdapterFactory;
use Schranz\Search\SEAL\Adapter\Typesense\TypesenseAdapterFactory;
use Schranz\Search\SEAL\Engine;
use Schranz\Search\SEAL\EngineInterface;
use Schranz\Search\SEAL\EngineRegistry;
use Schranz\Search\SEAL\Schema\Loader\LoaderInterface;
use Schranz\Search\SEAL\Schema\Loader\PhpFileLoader;
use Schranz\Search\SEAL\Schema\Schema;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Boot\DirectoriesInterface;
use Spiral\Boot\EnvironmentInterface;
use Spiral\Config\ConfiguratorInterface;
use Spiral\Console\Bootloader\ConsoleBootloader;
use Spiral\Core\Container;

/**
 * @experimental
 */
final class SearchBootloader extends Bootloader
{
    private const ADAPTER_FACTORIES = [
        AlgoliaAdapterFactory::class,
        ElasticsearchAdapterFactory::class,
        OpensearchAdapterFactory::class,
        MeilisearchAdapterFactory::class,
        MemoryAdapterFactory::class,
        RediSearchAdapterFactory::class,
        SolrAdapterFactory::class,
        TypesenseAdapterFactory::class,
    ];

    /**
     * @param ConfiguratorInterface<SearchConfig> $config
     */
    public function __construct(
        private readonly ConfiguratorInterface $config,
    ) {
    }

    public function init(
        ConsoleBootloader $console,
        DirectoriesInterface $dirs,
        EnvironmentInterface $environment,
    ): void {
        $console->addCommand(IndexCreateCommand::class);
        $console->addCommand(IndexDropCommand::class);

        $this->config->setDefaults(
            SearchConfig::CONFIG,
            [
                'prefix' => $environment->get('SEAL_SEARCH_PREFIX', ''),
                'schemas' => [
                    'app' => [
                        'dir' => $dirs->get('app') . 'schemas',
                    ],
                ],
                'engines' => [],
            ],
        );
    }

    public function boot(Container $container, SearchConfig $config): void
    {
        $this->createAdapterFactories($container);

        $engineSchemaDirs = [];
        foreach ($config->getSchemas() as $options) {
            $engineSchemaDirs[$options['engine'] ?? 'default'][] = $options['dir'];
        }

        $engines = $config->getEngines();

        $engineServices = [];
        foreach ($engines as $name => $engineConfig) {
            $adapterServiceId = 'schranz_search.adapter.' . $name;
            $engineServiceId = 'schranz_search.engine.' . $name;
            $schemaLoaderServiceId = 'schranz_search.schema_loader.' . $name;
            $schemaId = 'schranz_search.schema.' . $name;

            /** @var string $adapterDsn */
            $adapterDsn = $engineConfig['adapter'];
            $dirs = $engineSchemaDirs[$name] ?? [];

            $container->bindSingleton(
                $adapterServiceId,
                static fn (AdapterFactory $factory): AdapterInterface => $factory->createAdapter($adapterDsn),
            );

            $container->bindSingleton(
                $schemaLoaderServiceId,
                static fn (Container $container): PhpFileLoader => new PhpFileLoader($dirs, $config->getPrefix()),
            );

            $container->bindSingleton(
                $schemaId,
                static function (Container $container) use ($schemaLoaderServiceId): Schema {
                    /** @var LoaderInterface $loader */
                    $loader = $container->get($schemaLoaderServiceId);

                    return $loader->load();
                },
            );

            $engineServices[$name] = $engineServiceId;
            $container->bindSingleton(
                $engineServiceId,
                static function (Container $container) use ($adapterServiceId, $schemaId): EngineInterface {
                    /** @var AdapterInterface $adapter */
                    $adapter = $container->get($adapterServiceId);
                    /** @var Schema $schema */
                    $schema = $container->get($schemaId);

                    return new Engine($adapter, $schema);
                },
            );

            if ('default' === $name || (!isset($engines['default']) && !$container->has(EngineInterface::class))) {
                $container->bind(EngineInterface::class, $engineServiceId);
            }
        }

        $container->bindSingleton(
            EngineRegistry::class,
            static function (Container $container) use ($engineServices): EngineRegistry {
                $engines = [];

                foreach ($engineServices as $name => $engineServiceId) {
                    /** @var EngineInterface $engine */
                    $engine = $container->get($engineServiceId);

                    $engines[$name] = $engine;
                }

                return new EngineRegistry($engines);
            },
        );
    }

    private function createAdapterFactories(Container $container): void
    {
        $adapterServices = []; // TODO tagged services would make this extensible

        foreach (self::ADAPTER_FACTORIES as $adapterClass) {
            if (!\class_exists($adapterClass)) {
                continue;
            }

            $container->bindSingleton($adapterClass, $adapterClass);
            $adapterServices[$adapterClass::getName()] = $adapterClass;
        }

        // ...

        $prefix = 'schranz_search.adapter.';

        $wrapperAdapters = [
            ReadWriteAdapterFactory::class,
            MultiAdapterFactory::class,
        ];

        foreach ($wrapperAdapters as $adapterClass) {
            if (!\class_exists($adapterClass)) {
                continue;
            }

            $container->bindSingleton(
                $adapterClass,
                static fn (Container $container): AdapterFactoryInterface => new $adapterClass($container, $prefix),
            );

            $adapterServices[$adapterClass::getName()] = $adapterClass;
        }

        // ...

        $container->bindSingleton(
            AdapterFactory::class,
            static function (Container $container) use ($adapterServices): AdapterFactory {
                $factories = [];
                foreach ($adapterServices as $name => $adapterServiceId) {
                    /** @var AdapterFactoryInterface $adapterFactory */
                    $adapterFactory = $container->get($adapterServiceId);

                    $factories[$name] = $adapterFactory;
                }

                return new AdapterFactory($factories);
            },
        );
    }
}
