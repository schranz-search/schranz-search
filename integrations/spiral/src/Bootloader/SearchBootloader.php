<?php

namespace Schranz\Search\Integration\Spiral\Bootloader;

use Schranz\Search\Integration\Spiral\Config\SearchConfig;
use Schranz\Search\SEAL\Adapter\AdapterFactory;
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
use Spiral\Core\Container;
use Spiral\Config\ConfiguratorInterface;

class SearchBootloader extends Bootloader
{
    public function __construct(
        private readonly ConfiguratorInterface $config
    ) {
    }

    public function init(): void {
        $this->config->setDefaults(
            SearchConfig::CONFIG,
            [
                'schemas' => [
                    'app' => [
                        'dir' => 'app/schemas',
                    ],
                ],
            ],
        );
    }

    public function boot(Container $container): void
    {
        $this->createAdapterFactories($container);

        /** @var SearchConfig $config */
        $config = $container->get(SearchConfig::class);

        $engineSchemaDirs = [];
        foreach ($config->getSchemas() as $options) {
            $engineSchemaDirs[$options['engine'] ?? 'default'][] = $options['dir'];
        }

        $engineServices = [];
        foreach ($config->getEngines() as $name => $engineConfig) {
            $adapterServiceId = 'schranz_search.adapter.' . $name;
            $engineServiceId = 'schranz_search.engine.' . $name;
            $schemaLoaderServiceId = 'schranz_search.schema_loader.' . $name;
            $schemaId = 'schranz_search.schema.' . $name;

            /** @var string $adapterDsn */
            $adapterDsn = $engineConfig['adapter'];
            $dirs = $engineSchemaDirs[$name] ?? [];

            $container->bindSingleton($adapterServiceId, function (Container $container) use ($adapterDsn) {
                /** @var AdapterFactory $factory */
                $factory = $container->get('schranz_search.adapter_factory');

                return $factory->createAdapter($adapterDsn);
            });

            $container->bindSingleton($schemaLoaderServiceId, function (Container $container) use ($dirs) {
                return new PhpFileLoader($dirs);
            });

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
                $engines[$name] = $container->get($engineServiceId);
            }

            return new EngineRegistry($engines);
        });
    }

    private function createAdapterFactories(Container $container): void
    {
        $adapterServices = []; // TODO tagged services would make this extensible

        if (\class_exists(AlgoliaAdapterFactory::class)) {
            $container->bindSingleton(AlgoliaAdapterFactory::class, function (Container $container) {
                return new AlgoliaAdapterFactory($container);
            });

            $adapterServices[AlgoliaAdapterFactory::getName()] = AlgoliaAdapterFactory::class;
        }

        if (\class_exists(ElasticsearchAdapterFactory::class)) {
            $container->bindSingleton(ElasticsearchAdapterFactory::class, function (Container $container) {
                return new ElasticsearchAdapterFactory($container);
            });

            $adapterServices[ElasticsearchAdapterFactory::getName()] = ElasticsearchAdapterFactory::class;
        }

        if (\class_exists(OpensearchAdapterFactory::class)) {
            $container->bindSingleton(OpensearchAdapterFactory::class, function (Container $container) {
                return new OpensearchAdapterFactory($container);
            });

            $adapterServices[OpensearchAdapterFactory::getName()] = OpensearchAdapterFactory::class;
        }

        if (\class_exists(MeilisearchAdapterFactory::class)) {
            $container->bindSingleton(MeilisearchAdapterFactory::class, function (Container $container) {
                return new MeilisearchAdapterFactory($container);
            });

            $adapterServices[MeilisearchAdapterFactory::getName()] = MeilisearchAdapterFactory::class;
        }

        if (\class_exists(MemoryAdapterFactory::class)) {
            $container->bindSingleton(MemoryAdapterFactory::class, function (Container $container) {
                return new MemoryAdapterFactory();
            });

            $adapterServices[MemoryAdapterFactory::getName()] = MemoryAdapterFactory::class;
        }

        if (\class_exists(RediSearchAdapterFactory::class)) {
            $container->bindSingleton(RediSearchAdapterFactory::class, function (Container $container) {
                return new RediSearchAdapterFactory($container);
            });

            $adapterServices[RediSearchAdapterFactory::getName()] = RediSearchAdapterFactory::class;
        }

        if (\class_exists(SolrAdapterFactory::class)) {
            $container->bindSingleton(SolrAdapterFactory::class, function (Container $container) {
                return new SolrAdapterFactory($container);
            });

            $adapterServices[SolrAdapterFactory::getName()] = SolrAdapterFactory::class;
        }

        if (\class_exists(TypesenseAdapterFactory::class)) {
            $container->bindSingleton(TypesenseAdapterFactory::class, function (Container $container) {
                return new TypesenseAdapterFactory($container);
            });

            $adapterServices[TypesenseAdapterFactory::getName()] = TypesenseAdapterFactory::class;
        }

        // ...

        if (\class_exists(ReadWriteAdapterFactory::class)) {
            $container->bindSingleton(ReadWriteAdapterFactory::class, function (Container $container) {
                return new ReadWriteAdapterFactory(
                    $container,
                    'schranz_search.adapter.',
                );
            });

            $adapterServices[ReadWriteAdapterFactory::getName()] = ReadWriteAdapterFactory::class;
        }

        if (\class_exists(MultiAdapterFactory::class)) {
            $container->bindSingleton(MultiAdapterFactory::class, function (Container $container) {
                return new MultiAdapterFactory(
                    $container,
                    'schranz_search.adapter.',
                );
            });

            $adapterServices[MultiAdapterFactory::getName()] = MultiAdapterFactory::class;
        }

        // ...

        $container->bindSingleton(AdapterFactory::class, function (Container $container) use ($adapterServices) {
            $factories = [];
            foreach ($adapterServices as $name => $adapterServiceId) {
                $factories[$name] = $container->get($adapterServiceId);
            }

            return new AdapterFactory($factories);
        });
    }
}
