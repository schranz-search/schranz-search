<?php

declare(strict_types=1);

namespace AppTest;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

abstract class FunctionalTestCase extends TestCase
{
    protected \Mezzio\Application $app;

    protected ContainerInterface $container;

    protected function setUp(): void
    {
        /** @var \Laminas\ServiceManager\ServiceManager $container */
        $container = require __DIR__ . '/../../config/container.php';

        /** @var \Mezzio\Application $app */
        $app = $container->get(\Mezzio\Application::class);
        /** @var \Mezzio\MiddlewareFactory $factory */
        $factory = $container->get(\Mezzio\MiddlewareFactory::class);

        (require __DIR__ . '/../../config/pipeline.php')($app, $factory, $container);
        (require __DIR__ . '/../../config/routes.php')($app, $factory, $container);

        $this->container = $container;
        $this->app = $app;
    }
}
