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

namespace Schranz\Search\Integration\Mezzio;

use Schranz\Search\Integration\Mezzio\Service\CommandAbstractFactory;
use Schranz\Search\Integration\Mezzio\Service\SealContainer;
use Schranz\Search\Integration\Mezzio\Service\SealContainerFactory;
use Schranz\Search\Integration\Mezzio\Service\SealContainerServiceAbstractFactory;
use Schranz\Search\SEAL\Adapter\AdapterFactory;
use Schranz\Search\SEAL\Adapter\AdapterFactoryInterface;
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
use Schranz\Search\SEAL\EngineInterface;
use Schranz\Search\SEAL\EngineRegistry;

final class ConfigProvider
{
    /**
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        return [
            'laminas-cli' => $this->getCliConfig(),
            'dependencies' => $this->getDependencies(),
            'schranz_search' => [
                'adapter_factories' => $this->getAdapterFactories(), // we are going over a config as there are no tagged services in mezzio
                'index_name_prefix' => '',
                'schemas' => [],
                'engines' => [],
                'reindex_providers' => [],
            ],
        ];
    }

    /**
     * @return array{
     *     commands: array<string, class-string>
     * }
     */
    public function getCliConfig(): array
    {
        return [
            'commands' => [
                'schranz:search:index-create' => Command\IndexCreateCommand::class,
                'schranz:search:index-drop' => Command\IndexDropCommand::class,
                'schranz:search:reindex' => Command\ReindexCommand::class,
            ],
        ];
    }

    /**
     * @return array{
     *     factories: array<class-string, class-string>
     * }
     */
    public function getDependencies(): array
    {
        /** @var array<class-string, class-string> $adapterFactories */
        $adapterFactories = [];
        foreach ($this->getAdapterFactories() as $adapterFactoryClass) {
            $adapterFactories[$adapterFactoryClass] = SealContainerServiceAbstractFactory::class;
        }

        return [
            'factories' => [
                EngineRegistry::class => SealContainerServiceAbstractFactory::class,
                EngineInterface::class => SealContainerServiceAbstractFactory::class,
                AdapterFactory::class => SealContainerServiceAbstractFactory::class,
                SealContainer::class => SealContainerFactory::class,
                Command\IndexCreateCommand::class => CommandAbstractFactory::class,
                Command\IndexDropCommand::class => CommandAbstractFactory::class,
                Command\ReindexCommand::class => CommandAbstractFactory::class,
                ...$adapterFactories,
            ],
        ];
    }

    /**
     * @return array<string, class-string<AdapterFactoryInterface>>
     */
    private function getAdapterFactories(): array
    {
        $adapterFactories = [];

        if (\class_exists(AlgoliaAdapterFactory::class)) {
            $adapterFactories[AlgoliaAdapterFactory::getName()] = AlgoliaAdapterFactory::class;
        }

        if (\class_exists(ElasticsearchAdapterFactory::class)) {
            $adapterFactories[ElasticsearchAdapterFactory::getName()] = ElasticsearchAdapterFactory::class;
        }

        if (\class_exists(LoupeAdapterFactory::class)) {
            $adapterFactories[LoupeAdapterFactory::getName()] = LoupeAdapterFactory::class;
        }

        if (\class_exists(MeilisearchAdapterFactory::class)) {
            $adapterFactories[MeilisearchAdapterFactory::getName()] = MeilisearchAdapterFactory::class;
        }

        if (\class_exists(OpensearchAdapterFactory::class)) {
            $adapterFactories[OpensearchAdapterFactory::getName()] = OpensearchAdapterFactory::class;
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
