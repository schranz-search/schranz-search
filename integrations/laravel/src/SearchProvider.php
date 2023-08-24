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

namespace Schranz\Search\Integration\Laravel;

use Illuminate\Support\ServiceProvider;
use Schranz\Search\Integration\Laravel\Console\IndexCreateCommand;
use Schranz\Search\Integration\Laravel\Console\IndexDropCommand;
use Schranz\Search\Integration\Laravel\Console\ReindexCommand;
use Schranz\Search\SEAL\Adapter\AdapterFactory;
use Schranz\Search\SEAL\Adapter\AdapterFactoryInterface;
use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\Adapter\Algolia\AlgoliaAdapterFactory;
use Schranz\Search\SEAL\Adapter\Elasticsearch\ElasticsearchAdapterFactory;
use Schranz\Search\SEAL\Adapter\Loupe\LoupeAdapterFactory;
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

/**
 * @experimental
 */
final class SearchProvider extends ServiceProvider
{
    /**
     * @internal
     */
    public function register(): void
    {
        $this->publishes([
            \dirname(__DIR__) . '/config/schranz_search.php' => config_path('schranz_search.php'),
        ]);

        $this->mergeConfigFrom(\dirname(__DIR__) . '/config/schranz_search.php', 'schranz_search');
    }

    /**
     * @internal
     */
    public function boot(): void
    {
        $this->commands([
            IndexCreateCommand::class,
            IndexDropCommand::class,
            ReindexCommand::class,
        ]);

        /** @var array{schranz_search: mixed[]} $globalConfig */
        $globalConfig = $this->app->get('config');

        /**
         * @var array{
         *     index_name_prefix: string,
         *     engines: array<string, array{adapter: string}>,
         *     schemas: array<string, array{dir: string, engine?: string}>,
         * } $config
         */
        $config = $globalConfig['schranz_search'];
        $indexNamePrefix = $config['index_name_prefix'];
        $engines = $config['engines'];
        $schemas = $config['schemas'];

        $engineSchemaDirs = [];
        foreach ($schemas as $options) {
            $engineSchemaDirs[$options['engine'] ?? 'default'][] = $options['dir'];
        }

        $this->createAdapterFactories();
        $engineServices = [];

        foreach ($engines as $name => $engineConfig) {
            $adapterServiceId = 'schranz_search.adapter.' . $name;
            $engineServiceId = 'schranz_search.engine.' . $name;
            $schemaLoaderServiceId = 'schranz_search.schema_loader.' . $name;
            $schemaId = 'schranz_search.schema.' . $name;

            /** @var string $adapterDsn */
            $adapterDsn = $engineConfig['adapter'];
            $dirs = $engineSchemaDirs[$name] ?? [];

            $this->app->singleton($adapterServiceId, function ($app) use ($adapterDsn) {
                /** @var AdapterFactory $factory */
                $factory = $app['schranz_search.adapter_factory'];

                return $factory->createAdapter($adapterDsn);
            });

            $this->app->singleton($schemaLoaderServiceId, fn () => new PhpFileLoader($dirs, $indexNamePrefix));

            $this->app->singleton($schemaId, function ($app) use ($schemaLoaderServiceId) {
                /** @var LoaderInterface $loader */
                $loader = $app[$schemaLoaderServiceId];

                return $loader->load();
            });

            $engineServices[$name] = $engineServiceId;
            $this->app->singleton($engineServiceId, function ($app) use ($adapterServiceId, $schemaId) {
                /** @var AdapterInterface $adapter */
                $adapter = $app->get($adapterServiceId);
                /** @var Schema $schema */
                $schema = $app->get($schemaId);

                return new Engine($adapter, $schema);
            });

            if ('default' === $name || (!isset($engines['default']) && !$this->app->has(EngineInterface::class))) {
                $this->app->alias($engineServiceId, EngineInterface::class);
            }
        }

        $this->app->singleton('schranz_search.engine_factory', function ($app) use ($engineServices) {
            $engines = []; // TODO use tagged like in adapter factories
            foreach ($engineServices as $name => $engineServiceId) {
                $engines[$name] = $app->get($engineServiceId);
            }

            return new EngineRegistry($engines);
        });

        $this->app->alias('schranz_search.engine_factory', EngineRegistry::class);

        $this->app->when(ReindexCommand::class)
            ->needs('$reindexProviders')
            ->giveTagged('schranz_search.reindex_provider');

        $this->app->tagged('schranz_search.reindex_provider');
    }

    private function createAdapterFactories(): void
    {
        $this->app->singleton('schranz_search.adapter_factory', function ($app) {
            $factories = [];
            /** @var AdapterFactoryInterface $service */
            foreach ($app->tagged('schranz_search.adapter_factory') as $service) {
                $factories[$service::getName()] = $service;
            }

            return new AdapterFactory($factories);
        });

        if (\class_exists(AlgoliaAdapterFactory::class)) {
            $this->app->singleton('schranz_search.algolia.adapter_factory', fn ($app) => new AlgoliaAdapterFactory($app));

            $this->app->tag(
                'schranz_search.algolia.adapter_factory',
                'schranz_search.adapter_factory',
            );
        }

        if (\class_exists(ElasticsearchAdapterFactory::class)) {
            $this->app->singleton('schranz_search.elasticsearch.adapter_factory', fn ($app) => new ElasticsearchAdapterFactory($app));

            $this->app->tag(
                'schranz_search.elasticsearch.adapter_factory',
                'schranz_search.adapter_factory',
            );
        }

        if (\class_exists(LoupeAdapterFactory::class)) {
            $this->app->singleton('schranz_search.loupe.adapter_factory', fn ($app) => new LoupeAdapterFactory($app));

            $this->app->tag(
                'schranz_search.loupe.adapter_factory',
                'schranz_search.adapter_factory',
            );
        }

        if (\class_exists(OpensearchAdapterFactory::class)) {
            $this->app->singleton('schranz_search.opensearch.adapter_factory', fn ($app) => new OpensearchAdapterFactory($app));

            $this->app->tag(
                'schranz_search.opensearch.adapter_factory',
                'schranz_search.adapter_factory',
            );
        }

        if (\class_exists(MeilisearchAdapterFactory::class)) {
            $this->app->singleton('schranz_search.meilisearch.adapter_factory', fn ($app) => new MeilisearchAdapterFactory($app));

            $this->app->tag(
                'schranz_search.meilisearch.adapter_factory',
                'schranz_search.adapter_factory',
            );
        }

        if (\class_exists(MemoryAdapterFactory::class)) {
            $this->app->singleton('schranz_search.memory.adapter_factory', fn () => new MemoryAdapterFactory());

            $this->app->tag(
                'schranz_search.memory.adapter_factory',
                'schranz_search.adapter_factory',
            );
        }

        if (\class_exists(RediSearchAdapterFactory::class)) {
            $this->app->singleton('schranz_search.redis.adapter_factory', fn ($app) => new RediSearchAdapterFactory($app));

            $this->app->tag(
                'schranz_search.redis.adapter_factory',
                'schranz_search.adapter_factory',
            );
        }

        if (\class_exists(SolrAdapterFactory::class)) {
            $this->app->singleton('schranz_search.solr.adapter_factory', fn ($app) => new SolrAdapterFactory($app));

            $this->app->tag(
                'schranz_search.solr.adapter_factory',
                'schranz_search.adapter_factory',
            );
        }

        if (\class_exists(TypesenseAdapterFactory::class)) {
            $this->app->singleton('schranz_search.typesense.adapter_factory', fn ($app) => new TypesenseAdapterFactory($app));

            $this->app->tag(
                'schranz_search.typesense.adapter_factory',
                'schranz_search.adapter_factory',
            );
        }

        // ...

        if (\class_exists(ReadWriteAdapterFactory::class)) {
            $this->app->singleton('schranz_search.read_write.adapter_factory', fn ($app) => new ReadWriteAdapterFactory(
                $app,
                'schranz_search.adapter.',
            ));

            $this->app->tag(
                'schranz_search.read_write.adapter_factory',
                'schranz_search.adapter_factory',
            );
        }

        if (\class_exists(MultiAdapterFactory::class)) {
            $this->app->singleton('schranz_search.multi.adapter_factory', fn ($app) => new MultiAdapterFactory(
                $app,
                'schranz_search.adapter.',
            ));

            $this->app->tag(
                'schranz_search.multi.adapter_factory',
                'schranz_search.adapter_factory',
            );
        }
    }
}
