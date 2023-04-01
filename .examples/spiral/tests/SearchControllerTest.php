<?php

declare(strict_types=1);

namespace Tests;

use Spiral\Testing\Http\FakeHttp;
use Symfony\Component\DomCrawler\Crawler;

final class SearchControllerTest extends TestCase
{
    private FakeHttp $http;

    protected function setUp(): void
    {
        parent::setUp();

        $this->http = $this->fakeHttp();
    }

    public function testDefaultActionWorks(): void
    {
        $response = $this->http
            ->get('/')
            ->assertOk()
            ->assertBodyContains('<title>Search Engines</title>');

        $crawler = $this->crawler($response->__toString());

        $crawler->filter('a')->each(function ($node) {
            $response = $this->http
                ->get($node->attr('href'))
                ->assertOk();

            $crawler = $this->crawler($response->__toString());

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
