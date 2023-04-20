<?php

declare(strict_types=1);

namespace Schranz\Search\Integration\Mezzio\Service;

use Psr\Container\ContainerInterface;
use Schranz\Search\SEAL\Engine;
use Schranz\Search\SEAL\EngineRegistry;

/**
 * @internal
 */
final class EngineFactory
{
    public function __invoke(ContainerInterface $container): Engine
    {
        $engineFactory = $container->get(EngineRegistry::class);

        return $engineFactory->getEngine('default');
    }
}
