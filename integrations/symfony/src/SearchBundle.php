<?php

declare(strict_types=1);

namespace Schranz\Search\Integration\Symfony;

use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\Adapter\Multi\MultiAdapterFactory;
use Schranz\Search\SEAL\Adapter\ReadWrite\ReadWriteAdapterFactory;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

/**
 * @experimental
 */
class SearchBundle extends AbstractBundle
{
    protected string $extensionAlias = 'schranz_search';

    public function configure(DefinitionConfigurator $definition): void
    {
        // @phpstan-ignore-next-line
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
            ->end();
    }

    /**
     * @param array{
     *     connections: array<string, array{dsn: string}>,
     * } $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $connections = $config['connections'];

        foreach ($connections as $name => $connection) {
            $serviceId = 'schranz_search.connection.' . $name;

            $definition = $builder->register($serviceId, AdapterInterface::class)
                ->setFactory([new Reference('schranz_search.adapter_factory'), 'createAdapter'])
                ->setArguments([$connection['dsn']])
                ->addTag('schranz_search.adapter', ['name' => $name]);

            if (\class_exists(ReadWriteAdapterFactory::class) || \class_exists(MultiAdapterFactory::class)) {
                // the read-write and multi adapter require access all other adapters so they need to be public
                $definition->setPublic(true);
            }

            if ('default' === $name) {
                $builder->setAlias(AdapterInterface::class, $serviceId);
            }

            $builder->registerAliasForArgument(
                $serviceId,
                AdapterInterface::class,
                $name . 'Adapter',
            );
        }

        $container->import('../config/services.php');
    }
}
