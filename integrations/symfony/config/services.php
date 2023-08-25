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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Schranz\Search\Integration\Symfony\Command\IndexCreateCommand;
use Schranz\Search\Integration\Symfony\Command\IndexDropCommand;
use Schranz\Search\Integration\Symfony\Command\ReindexCommand;
use Schranz\Search\SEAL\Adapter\AdapterFactory;
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
use Schranz\Search\SEAL\EngineRegistry;

/*
 * @internal
 */
return static function (ContainerConfigurator $container) {
    // -------------------------------------------------------------------//
    // Commands                                                           //
    // -------------------------------------------------------------------//
    $container->services()
        ->set('schranz_search.index_create_command', IndexCreateCommand::class)
        ->args([
            service('schranz_search.engine_registry'),
        ])
        ->tag('console.command');

    $container->services()
        ->set('schranz_search.index_drop_command', IndexDropCommand::class)
        ->args([
            service('schranz_search.engine_registry'),
        ])
        ->tag('console.command');

    $container->services()
        ->set('schranz_search.reindex_command', ReindexCommand::class)
        ->args([
            service('schranz_search.engine_registry'),
            tagged_iterator('schranz_search.reindex_provider'),
        ])
        ->tag('console.command');

    // -------------------------------------------------------------------//
    // Services                                                           //
    // -------------------------------------------------------------------//
    $container->services()
        ->set('schranz_search.engine_registry', EngineRegistry::class)
        ->args([
            tagged_iterator('schranz_search.engine', 'name'),
        ])
        ->alias(EngineRegistry::class, 'schranz_search.engine_registry');

    $container->services()
        ->set('schranz_search.adapter_factory', AdapterFactory::class)
            ->args([
                tagged_iterator('schranz_search.adapter_factory', null, 'getName'),
            ])
        ->alias(AdapterFactory::class, 'schranz_search.adapter_factory');

    if (\class_exists(AlgoliaAdapterFactory::class)) {
        $container->services()
            ->set('schranz_search.algolia.adapter_factory', AlgoliaAdapterFactory::class)
            ->args([
                service('service_container'),
            ])
            ->tag('schranz_search.adapter_factory', ['name' => AlgoliaAdapterFactory::getName()]);
    }

    if (\class_exists(ElasticsearchAdapterFactory::class)) {
        $container->services()
            ->set('schranz_search.elasticsearch.adapter_factory', ElasticsearchAdapterFactory::class)
            ->args([
                service('service_container'),
            ])
            ->tag('schranz_search.adapter_factory', ['name' => ElasticsearchAdapterFactory::getName()]);
    }

    if (\class_exists(LoupeAdapterFactory::class)) {
        $container->services()
            ->set('schranz_search.loupe.adapter_factory', LoupeAdapterFactory::class)
            ->args([
                service('service_container'),
            ])
            ->tag('schranz_search.adapter_factory', ['name' => LoupeAdapterFactory::getName()]);
    }

    if (\class_exists(OpensearchAdapterFactory::class)) {
        $container->services()
            ->set('schranz_search.opensearch.adapter_factory', OpensearchAdapterFactory::class)
            ->args([
                service('service_container'),
            ])
            ->tag('schranz_search.adapter_factory', ['name' => OpensearchAdapterFactory::getName()]);
    }

    if (\class_exists(MeilisearchAdapterFactory::class)) {
        $container->services()
            ->set('schranz_search.meilisearch.adapter_factory', MeilisearchAdapterFactory::class)
            ->args([
                service('service_container'),
            ])
            ->tag('schranz_search.adapter_factory', ['name' => MeilisearchAdapterFactory::getName()]);
    }

    if (\class_exists(MemoryAdapterFactory::class)) {
        $container->services()
            ->set('schranz_search.memory.adapter_factory', MemoryAdapterFactory::class)
            ->args([
                service('service_container'),
            ])
            ->tag('schranz_search.adapter_factory', ['name' => MemoryAdapterFactory::getName()]);
    }

    if (\class_exists(RediSearchAdapterFactory::class)) {
        $container->services()
            ->set('schranz_search.redis.adapter_factory', RediSearchAdapterFactory::class)
            ->args([
                service('service_container'),
            ])
            ->tag('schranz_search.adapter_factory', ['name' => RediSearchAdapterFactory::getName()]);
    }

    if (\class_exists(SolrAdapterFactory::class)) {
        $container->services()
            ->set('schranz_search.solr.adapter_factory', SolrAdapterFactory::class)
            ->args([
                service('service_container'),
            ])
            ->tag('schranz_search.adapter_factory', ['name' => SolrAdapterFactory::getName()]);
    }

    if (\class_exists(TypesenseAdapterFactory::class)) {
        $container->services()
            ->set('schranz_search.typesense.adapter_factory', TypesenseAdapterFactory::class)
            ->args([
                service('service_container'),
            ])
            ->tag('schranz_search.adapter_factory', ['name' => TypesenseAdapterFactory::getName()]);
    }

    // ...

    if (\class_exists(ReadWriteAdapterFactory::class)) {
        $container->services()
            ->set('schranz_search.read_write.adapter_factory', ReadWriteAdapterFactory::class)
            ->args([
                service('service_container'),
                'schranz_search.adapter.',
            ])
            ->tag('schranz_search.adapter_factory', ['name' => ReadWriteAdapterFactory::getName()]);
    }

    if (\class_exists(MultiAdapterFactory::class)) {
        $container->services()
            ->set('schranz_search.multi.adapter_factory', MultiAdapterFactory::class)
            ->args([
                service('service_container'),
                'schranz_search.adapter.',
            ])
            ->tag('schranz_search.adapter_factory', ['name' => MultiAdapterFactory::getName()]);
    }
};
