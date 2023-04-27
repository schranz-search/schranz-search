<?php

declare(strict_types=1);

namespace Schranz\Search\Integration\Mezzio\Service;

use Psr\Container\ContainerInterface;
use Schranz\Search\SEAL\EngineRegistry;

/**
 * @internal
 */
final class CommandAbstractFactory
{
    /**
     * @template T of object
     *
     * @param class-string<T> $className
     *
     * @return T
     */
    public function __invoke(ContainerInterface $container, string $className): object
    {
        $engineRegistry = $container->get(EngineRegistry::class);

        return new $className($engineRegistry);
    }
}
