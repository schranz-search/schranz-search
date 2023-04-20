<?php

declare(strict_types=1);

namespace Schranz\Search\Integration\Mezzio;

use Schranz\Search\Integration\Mezzio\Service\AdapterFactoryAbstractFactory;
use Schranz\Search\Integration\Mezzio\Service\AdapterFactoryFactory;
use Schranz\Search\Integration\Mezzio\Service\EngineFactory;
use Schranz\Search\Integration\Mezzio\Service\EngineRegistryFactory;
use Schranz\Search\SEAL\Adapter\AdapterFactory;
use Schranz\Search\SEAL\Adapter\Algolia\AlgoliaAdapterFactory;
use Schranz\Search\SEAL\Adapter\Elasticsearch\ElasticsearchAdapterFactory;
use Schranz\Search\SEAL\Adapter\Meilisearch\MeilisearchAdapterFactory;
use Schranz\Search\SEAL\Adapter\Memory\MemoryAdapterFactory;
use Schranz\Search\SEAL\Adapter\Multi\MultiAdapterFactory;
use Schranz\Search\SEAL\Adapter\ReadWrite\ReadWriteAdapterFactory;
use Schranz\Search\SEAL\Adapter\RediSearch\RediSearchAdapterFactory;
use Schranz\Search\SEAL\Adapter\Solr\SolrAdapterFactory;
use Schranz\Search\SEAL\Adapter\Typesense\TypesenseAdapterFactory;
use Schranz\Search\SEAL\Engine;
use Schranz\Search\SEAL\EngineRegistry;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'schranz_search' => [
                'adapter_factories' => $this->getAdapterFactories(), // we are going over a config as there are no tagged services in mezzio
                'prefix' => '',
                'schemas' => [],
                'engines' => [],
            ],
        ];
    }

    public function getDependencies(): array
    {
        $adapterFactories = [];
        foreach ($this->getAdapterFactories() as $adapterFactoryClass) {
            $adapterFactories[$adapterFactoryClass] = AdapterFactoryAbstractFactory::class;
        }

        return [
            'factories' => [
                EngineRegistry::class => EngineRegistryFactory::class,
                Engine::class => EngineFactory::class,
                AdapterFactory::class => AdapterFactoryFactory::class,
                ...$adapterFactories,
            ],
        ];
    }

    private function getAdapterFactories()
    {
        $adapterFactories = [];

        if (\class_exists(AlgoliaAdapterFactory::class)) {
            $adapterFactories[AlgoliaAdapterFactory::getName()] = AlgoliaAdapterFactory::class;
        }

        if (\class_exists(ElasticsearchAdapterFactory::class)) {
            $adapterFactories[ElasticsearchAdapterFactory::getName()] = ElasticsearchAdapterFactory::class;
        }

        if (\class_exists(MeilisearchAdapterFactory::class)) {
            $adapterFactories[MeilisearchAdapterFactory::getName()] = MeilisearchAdapterFactory::class;
        }

        if (\class_exists(MemoryAdapterFactory::class)) {
            $adapterFactories[MemoryAdapterFactory::getName()] = MemoryAdapterFactory::class;
        }

        if (\class_exists(RediSearchAdapterFactory::class)) {
            $adapterFactories[RediSearchAdapterFactory::getName()] = RediSearchAdapterFactory::class;
        }

        if (\class_exists(SolrAdapterFactory::class)) {
            $adapterFactories[SolrAdapterFactory::getName()] = SolrAdapterFactory::class;
        }

        if (\class_exists(TypesenseAdapterFactory::class)) {
            $adapterFactories[TypesenseAdapterFactory::getName()] = TypesenseAdapterFactory::class;
        }

        if (\class_exists(ReadWriteAdapterFactory::class)) {
            $adapterFactories[ReadWriteAdapterFactory::getName()] = ReadWriteAdapterFactory::class;
        }

        if (\class_exists(MultiAdapterFactory::class)) {
            $adapterFactories[MultiAdapterFactory::getName()] = MultiAdapterFactory::class;
        }

        return $adapterFactories;
    }
}
