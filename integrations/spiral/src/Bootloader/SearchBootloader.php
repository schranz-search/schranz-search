<?php

declare(strict_types=1);

namespace Schranz\Search\Integration\Spiral\Bootloader;

use Schranz\Search\Integration\Spiral\Config\SearchConfig;
use Schranz\Search\Integration\Spiral\Console\IndexCreateCommand;
use Schranz\Search\Integration\Spiral\Console\IndexDropCommand;
use Schranz\Search\SEAL\Adapter\AdapterFactory;
use Schranz\Search\SEAL\Adapter\AdapterFactoryInterface;
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
use Schranz\Search\SEAL\Schema\Loader\PhpFileLoader;
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
    protected const SINGLETONS = [
        AdapterFactory::class => [self::class, 'createAdapterFactory'],
        EngineRegistry::class => [self::class, 'createEngineRegistry'],
        Engine::class => [self::class, 'defaultEngine'],
    ];

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
                        'dir' => $dirs->get('app').'schemas',
                    ],
                ],
                'engines' => [],
            ],
        );
    }

    private function createEngineRegistry(
        SearchConfig $config,
        AdapterFactory $adapterFactory,
        Container $container,
    ): EngineRegistry {
        $engineSchemaDirs = [];
        foreach ($config->getSchemas() as $options) {
            $engineSchemaDirs[$options['engine'] ?? 'default'][] = $options['dir'];
        }

        $engines = $config->getEngines();

        $engineServices = [];
        foreach ($engines as $name => $engineConfig) {
            $adapterServiceId = 'schranz_search.adapter.'.$name;
            $engineServiceId = 'schranz_search.engine.'.$name;
            $schemaLoaderServiceId = 'schranz_search.schema_loader.'.$name;
            $schemaId = 'schranz_search.schema.'.$name;

            /** @var string $adapterDsn */
            $adapterDsn = $engineConfig['adapter'];
            $dirs = $engineSchemaDirs[$name] ?? [];

            $container->bindSingleton($adapterServiceId, $adapter = $adapterFactory->createAdapter($adapterDsn));
            $container->bindSingleton($schemaLoaderServiceId, $loader = new PhpFileLoader($dirs, $config->getPrefix()));
            $container->bindSingleton($schemaId, $schema = $loader->load());
            $container->bindSingleton($engineServiceId, $engine = new Engine($adapter, $schema));

            $engineServices[$name] = $engine;
        }

        return new EngineRegistry($engineServices);
    }

    private function defaultEngine(EngineRegistry $registry): Engine
    {
        $engines = $registry->getEngines();

        if (\count($engines) === 0) {
            throw new \RuntimeException('No search engines configured.');
        }

        return $engines['default'] ?? \reset($engines);
    }

    private function createAdapterFactory(Container $container): AdapterFactory
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

        if (\class_exists(ReadWriteAdapterFactory::class)) {
            $container->bindSingleton(
                ReadWriteAdapterFactory::class,
                static fn (Container $container) => new ReadWriteAdapterFactory($container, $prefix),
            );

            $adapterServices[ReadWriteAdapterFactory::getName()] = ReadWriteAdapterFactory::class;
        }

        if (\class_exists(MultiAdapterFactory::class)) {
            $container->bindSingleton(
                MultiAdapterFactory::class,
                static fn (Container $container) => new MultiAdapterFactory($container, $prefix),
            );

            $adapterServices[MultiAdapterFactory::getName()] = MultiAdapterFactory::class;
        }

        // ...

        $factories = [];
        foreach ($adapterServices as $name => $adapterServiceId) {
            /** @var AdapterFactoryInterface $adapterFactory */
            $adapterFactory = $container->get($adapterServiceId);

            $factories[$name] = $adapterFactory;
        }

        return new AdapterFactory($factories);
    }
}
