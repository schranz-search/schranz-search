<?php

declare(strict_types=1);

namespace Tests;

final class CommandTest extends TestCase
{
    public function testCreate(): void
    {
        $this->assertCommandRegistered('schranz:search:index-create');
        $output = $this->runCommand('schranz:search:index-create');

        $this->assertStringContainsString('Search indexes created.', $output);
    }

    public function testDrop(): void
    {
        $this->assertCommandRegistered('schranz:search:index-drop');
        $output = $this->runCommand('schranz:search:index-drop', ['--force' => true]);

        $this->assertStringContainsString('Search indexes dropped.', $output);
    }
}
