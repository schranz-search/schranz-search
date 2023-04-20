<?php

declare(strict_types=1);

namespace AppTest\Handler;

use App\Handler\HomePageHandler;
use App\Handler\HomePageHandlerFactory;
use Mezzio\Router\RouterInterface;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class HomePageHandlerFactoryTest extends TestCase
{
    /** @var ContainerInterface&MockObject */
    protected $container;

    /** @var RouterInterface&MockObject */
    protected $router;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->router    = $this->createMock(RouterInterface::class);
    }

    public function testFactoryWithoutTemplate(): void
    {
        $this->container
            ->expects($this->once())
            ->method('has')
            ->with(TemplateRendererInterface::class)
            ->willReturn(false);
        $this->container
            ->expects($this->once())
            ->method('get')
            ->with(RouterInterface::class)
            ->willReturn($this->router);

        $factory  = new HomePageHandlerFactory();
        $homePage = $factory($this->container);

        self::assertInstanceOf(HomePageHandler::class, $homePage);
    }

    public function testFactoryWithTemplate(): void
    {
        $renderer = $this->createMock(TemplateRendererInterface::class);
        $this->container
            ->expects($this->once())
            ->method('has')
            ->with(TemplateRendererInterface::class)
            ->willReturn(true);
        $this->container
            ->expects($this->exactly(2))
            ->method('get')
            ->withConsecutive(
                [RouterInterface::class],
                [TemplateRendererInterface::class],
            )
            ->willReturnOnConsecutiveCalls(
                $this->router,
                $renderer
            );

        $factory  = new HomePageHandlerFactory();
        $homePage = $factory($this->container);

        self::assertInstanceOf(HomePageHandler::class, $homePage);
    }
}
