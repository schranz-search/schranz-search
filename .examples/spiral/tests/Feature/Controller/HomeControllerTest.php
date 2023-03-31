<?php

declare(strict_types=1);

namespace Tests\Feature\Controller;

use Tests\TestCase;
use Spiral\Testing\Http\FakeHttp;

class HomeControllerTest extends TestCase
{
    private FakeHttp $http;

    protected function setUp(): void
    {
        parent::setUp();

        $this->http = $this->fakeHttp();
    }

    public function testDefaultActionWorks(): void
    {
        $this->http
            ->get('/')
            ->assertOk()
            ->assertBodyContains('Welcome to Spiral Framework');
    }

    public function testDefaultActionWithRuLocale(): void
    {
        $this->http
            ->withHeader('accept-language', 'ru')
            ->get('/')
            ->assertOk()
            ->assertBodyContains('Вас приветствует Spiral Framework');
    }

    public function testInteractWithConsole(): void
    {
        $output = $this->runCommand('views:reset');

        $this->assertStringContainsString('cache', $output);
    }
}
