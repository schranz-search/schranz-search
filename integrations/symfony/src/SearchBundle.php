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

namespace Schranz\Search\Integration\Symfony;

use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\Adapter\Multi\MultiAdapterFactory;
use Schranz\Search\SEAL\Adapter\ReadWrite\ReadWriteAdapterFactory;
use Schranz\Search\SEAL\Engine;
use Schranz\Search\SEAL\EngineInterface;
use Schranz\Search\SEAL\Reindex\ReindexProviderInterface;
use Schranz\Search\SEAL\Schema\Loader\PhpFileLoader;
use Schranz\Search\SEAL\Schema\Schema;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * @experimental
 */
final class SearchBundle extends AbstractBundle
{
    protected string $extensionAlias = 'schranz_search';

    public function configure(DefinitionConfigurator $definition): void
    {
        // @phpstan-ignore-next-line
        $definition->rootNode()
            ->children()
                ->scalarNode('index_name_prefix')->defaultValue('')->end()
                ->arrayNode('schemas')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('dir')->end()
                            ->scalarNode('engine')->defaultNull()->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('engines')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('adapter')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param array{
     *     index_name_prefix: string,
     *     engines: array<string, array{adapter: string}>,
     *     schemas: array<string, array{dir: string, engine?: string}>,
     * } $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $indexNamePrefix = $config['index_name_prefix'];
        $engines = $config['engines'];
        $schemas = $config['schemas'];

        $engineSchemaDirs = [];
        foreach ($schemas as $options) {
            $engineSchemaDirs[$options['engine'] ?? 'default'][] = $options['dir'];
        }

        foreach ($engines as $name => $engineConfig) {
            $adapterServiceId = 'schranz_search.adapter.' . $name;
            $engineServiceId = 'schranz_search.engine.' . $name;
            $schemaLoaderServiceId = 'schranz_search.schema_loader.' . $name;
            $schemaId = 'schranz_search.schema.' . $name;

            $definition = $builder->register($adapterServiceId, AdapterInterface::class)
                ->setFactory([new Reference('schranz_search.adapter_factory'), 'createAdapter'])
                ->setArguments([$engineConfig['adapter']])
                ->addTag('schranz_search.adapter', ['name' => $name]);

            if (\class_exists(ReadWriteAdapterFactory::class) || \class_exists(MultiAdapterFactory::class)) {
                // the read-write and multi adapter require access all other adapters so they need to be public
                $definition->setPublic(true);
            }

            $dirs = $engineSchemaDirs[$name] ?? [];

            $builder->register($schemaLoaderServiceId, PhpFileLoader::class)
                ->setArguments([$dirs, $indexNamePrefix]);

            $builder->register($schemaId, Schema::class)
                ->setFactory([new Reference($schemaLoaderServiceId), 'load']);

            $builder->register($engineServiceId, Engine::class)
                ->setArguments([
                    new Reference($adapterServiceId),
                    new Reference($schemaId),
                ])
                ->addTag('schranz_search.engine', ['name' => $name]);

            if ('default' === $name || (!isset($engines['default']) && !$builder->has(EngineInterface::class))) {
                $builder->setAlias(EngineInterface::class, $engineServiceId);
                $builder->setAlias(Schema::class, $schemaId);
            }

            $builder->registerAliasForArgument(
                $engineServiceId,
                EngineInterface::class,
                $name . 'Engine',
            );

            $builder->registerAliasForArgument(
                $schemaId,
                Schema::class,
                $name . 'Schema',
            );
        }

        $builder->registerForAutoconfiguration(ReindexProviderInterface::class)
            ->addTag('schranz_search.reindex_provider');

        $container->import(\dirname(__DIR__) . '/config/services.php');
    }
}
