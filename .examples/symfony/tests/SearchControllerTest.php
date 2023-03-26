<?php

declare(strict_types=1);

namespace App\Tests;

use App\Controller\SearchController;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

#[CoversClass(SearchController::class)]
class SearchControllerTest extends WebTestCase
{
    public function testSearch(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('title', 'Search Engines');

        $crawler->filter('a')->each(function ($node) use ($client) {
            $crawler = $client->request('GET', $node->link()->getUri());
            $this->assertResponseIsSuccessful();

            $this->assertSelectorTextContains('title', $node->text());

            $this->assertCount(1, $crawler->filter('h1'));
        });
    }
}
