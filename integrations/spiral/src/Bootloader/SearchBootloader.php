<?php

declare(strict_types=1);

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
use Schranz\Search\SEAL\EngineRegistry;
use Schranz\Search\SEAL\Schema\Loader\LoaderInterface;
use Schranz\Search\SEAL\Schema\Loader\PhpFileLoader;
use Schranz\Search\SEAL\Schema\Schema;
use Spiral\Boot\Bootloader\Bootloader;
use Spiral\Config\ConfiguratorInterface;
use Spiral\Console\Bootloader\ConsoleBootloader;
use Spiral\Core\Container;

/**
 * @experimental
 */
final class SearchBootloader extends Bootloader
{
    protected const DEPENDENCIES = [
        ConsoleBootloader::class,
    ];

    /**
     * @param ConfiguratorInterface<SearchConfig> $config
     */
    public function __construct(
        private readonly ConfiguratorInterface $config,
    ) {
    }

    public function init(): void
    {
        $this->config->setDefaults(
            SearchConfig::CONFIG,
            [
                'prefix' => '',
                'schemas' => [
                    'app' => [
                        'dir' => 'app/schemas',
                    ],
                ],
                'engines' => [],
            ],
        );
    }

    public function boot(ConsoleBootloader $console, Container $container): void
    {
        $console->addCommand(IndexCreateCommand::class);
        $console->addCommand(IndexDropCommand::class);

        $this->createAdapterFactories($container);

        /** @var SearchConfig $config */
        $config = $container->get(SearchConfig::class);

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

            $container->bindSingleton($adapterServiceId, function (Container $container) use ($adapterDsn) {
                /** @var AdapterFactory $factory */
                $factory = $container->get(AdapterFactory::class);

                return $factory->createAdapter($adapterDsn);
            });

            $container->bindSingleton($schemaLoaderServiceId, fn (Container $container) => new PhpFileLoader($dirs, $config->getPrefix()));

            $container->bindSingleton($schemaId, function (Container $container) use ($schemaLoaderServiceId) {
                /** @var LoaderInterface $loader */
                $loader = $container->get($schemaLoaderServiceId);

                return $loader->load();
            });

            $engineServices[$name] = $engineServiceId;
            $container->bindSingleton($engineServiceId, function (Container $container) use ($adapterServiceId, $schemaId) {
                /** @var AdapterInterface $adapter */
                $adapter = $container->get($adapterServiceId);
                /** @var Schema $schema */
                $schema = $container->get($schemaId);

                return new Engine($adapter, $schema);
            });

            if ('default' === $name || (!isset($engines['default']) && !$container->has(Engine::class))) {
                $container->bind(Engine::class, $engineServiceId);
            }
        }

        $container->bindSingleton(EngineRegistry::class, function (Container $container) use ($engineServices) {
            $engines = [];

            foreach ($engineServices as $name => $engineServiceId) {
                /** @var Engine $engine */
                $engine = $container->get($engineServiceId);

                $engines[$name] = $engine;
            }

            return new EngineRegistry($engines);
        });
    }

    private function createAdapterFactories(Container $container): void
    {
        $adapterServices = []; // TODO tagged services would make this extensible

        if (\class_exists(AlgoliaAdapterFactory::class)) {
            $container->bindSingleton(AlgoliaAdapterFactory::class, fn (Container $container) => new AlgoliaAdapterFactory($container));

            $adapterServices[AlgoliaAdapterFactory::getName()] = AlgoliaAdapterFactory::class;
        }

        if (\class_exists(ElasticsearchAdapterFactory::class)) {
            $container->bindSingleton(ElasticsearchAdapterFactory::class, fn (Container $container) => new ElasticsearchAdapterFactory($container));

            $adapterServices[ElasticsearchAdapterFactory::getName()] = ElasticsearchAdapterFactory::class;
        }

        if (\class_exists(OpensearchAdapterFactory::class)) {
            $container->bindSingleton(OpensearchAdapterFactory::class, fn (Container $container) => new OpensearchAdapterFactory($container));

            $adapterServices[OpensearchAdapterFactory::getName()] = OpensearchAdapterFactory::class;
        }

        if (\class_exists(MeilisearchAdapterFactory::class)) {
            $container->bindSingleton(MeilisearchAdapterFactory::class, fn (Container $container) => new MeilisearchAdapterFactory($container));

            $adapterServices[MeilisearchAdapterFactory::getName()] = MeilisearchAdapterFactory::class;
        }

        if (\class_exists(MemoryAdapterFactory::class)) {
            $container->bindSingleton(MemoryAdapterFactory::class, fn (Container $container) => new MemoryAdapterFactory());

            $adapterServices[MemoryAdapterFactory::getName()] = MemoryAdapterFactory::class;
        }

        if (\class_exists(RediSearchAdapterFactory::class)) {
            $container->bindSingleton(RediSearchAdapterFactory::class, fn (Container $container) => new RediSearchAdapterFactory($container));

            $adapterServices[RediSearchAdapterFactory::getName()] = RediSearchAdapterFactory::class;
        }

        if (\class_exists(SolrAdapterFactory::class)) {
            $container->bindSingleton(SolrAdapterFactory::class, fn (Container $container) => new SolrAdapterFactory($container));

            $adapterServices[SolrAdapterFactory::getName()] = SolrAdapterFactory::class;
        }

        if (\class_exists(TypesenseAdapterFactory::class)) {
            $container->bindSingleton(TypesenseAdapterFactory::class, fn (Container $container) => new TypesenseAdapterFactory($container));

            $adapterServices[TypesenseAdapterFactory::getName()] = TypesenseAdapterFactory::class;
        }

        // ...

        if (\class_exists(ReadWriteAdapterFactory::class)) {
            $container->bindSingleton(ReadWriteAdapterFactory::class, fn (Container $container) => new ReadWriteAdapterFactory(
                $container,
                'schranz_search.adapter.',
            ));

            $adapterServices[ReadWriteAdapterFactory::getName()] = ReadWriteAdapterFactory::class;
        }

        if (\class_exists(MultiAdapterFactory::class)) {
            $container->bindSingleton(MultiAdapterFactory::class, fn (Container $container) => new MultiAdapterFactory(
                $container,
                'schranz_search.adapter.',
            ));

            $adapterServices[MultiAdapterFactory::getName()] = MultiAdapterFactory::class;
        }

        // ...

        $container->bindSingleton(AdapterFactory::class, function (Container $container) use ($adapterServices) {
            $factories = [];
            foreach ($adapterServices as $name => $adapterServiceId) {
                /** @var AdapterFactoryInterface $adapterFactory */
                $adapterFactory = $container->get($adapterServiceId);

                $factories[$name] = $adapterFactory;
            }

            return new AdapterFactory($factories);
        });
    }
}
