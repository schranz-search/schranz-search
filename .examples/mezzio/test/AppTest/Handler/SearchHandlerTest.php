<?php

declare(strict_types=1);

namespace AppTest\Handler;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Symfony\Component\DomCrawler\Crawler;

class SearchHandlerTest extends TestCase
{
    private \Mezzio\Application $app;

    private ContainerInterface $container;

    private ServerRequestFactoryInterface $requestFactory;

    protected function setUp(): void
    {
        /** @var \Laminas\ServiceManager\ServiceManager $container */
        $container = require __DIR__ . '/../../../config/container.php';

        /** @var \Mezzio\Application $app */
        $app = $container->get(\Mezzio\Application::class);
        /** @var \Mezzio\MiddlewareFactory $factory */
        $factory = $container->get(\Mezzio\MiddlewareFactory::class);

        (require __DIR__ . '/../../../config/pipeline.php')($app, $factory, $container);
        (require __DIR__ . '/../../../config/routes.php')($app, $factory, $container);

        $this->container = $container;
        $this->app = $app;

        /** @var ServerRequestFactoryInterface $requestFactory */
        $requestFactory = $this->container->get(ServerRequestFactoryInterface::class);

        $this->requestFactory = $requestFactory;
    }

    public function testResponse(): void
    {
        $request = $this->requestFactory->createServerRequest('GET', '/');
        $response = $this->app->handle($request);

        self::assertSame(200, $response->getStatusCode());
        $content = $response->getBody()->__toString();
        self::assertStringContainsString('<title>Search Engines</title>', $content);

        $crawler = $this->crawler($content);
        $crawler->filter('a')->each(function ($node) {
            $request = $this->requestFactory->createServerRequest('GET', $node->attr('href'));
            $response = $this->app->handle($request);
            self::assertSame(200, $response->getStatusCode());
            $content = $response->getBody()->__toString();

            $crawler = $this->crawler($content);

            $this->assertStringContainsString($node->text(), $crawler->filter('title')->first()->text());

            $h1s = $crawler->filter('h1');
            $this->assertCount(1, $h1s);

            $h1 = $h1s->first();

            $this->assertStringContainsString(\str_replace('-', '', (string) $node->text()), $h1->text());
        });
    }

    private function crawler(string $content): Crawler
    {
        return new Crawler($content);
    }
}
