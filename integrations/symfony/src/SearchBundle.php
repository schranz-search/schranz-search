<?php

namespace Schranz\Search\Integration\Symfony;

use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\Adapter\Multi\MultiAdapterFactory;
use Schranz\Search\SEAL\Adapter\ReadWrite\ReadWriteAdapterFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

/**
 * @experimental
 */
class SearchBundle extends AbstractBundle
{
    protected string $extensionAlias = 'schranz_search';

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->arrayNode('connections')
                    ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('dsn')->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $connections = $config['connections'];

        foreach ($connections as $name => $connection) {
            $serviceId = 'schranz_search.connection.' . $name;

            $definition = $builder->register($serviceId, AdapterInterface::class)
                ->setFactory([new Reference('schranz_search.adapter_factory'), 'createAdapter'])
                ->setArguments([$connection['dsn']])
                ->addTag('schranz_search.adapter', ['name' => $name])
            ;

            if (\class_exists(ReadWriteAdapterFactory::class) || \class_exists(MultiAdapterFactory::class)) {
                // the read-write and multi adapter require access all other adapters so they need to be public
                $definition->setPublic(true);
            }

            if ($name === 'default') {
                $builder->setAlias(AdapterInterface::class, $serviceId);
            }

            $builder->registerAliasForArgument(
                $serviceId,
                AdapterInterface::class,
                $name . 'Adapter'
            );
        }

        $container->import('../config/services.php');
    }
}
