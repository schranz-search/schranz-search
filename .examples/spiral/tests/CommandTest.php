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

    public function testReindex(): void
    {
        $this->assertCommandRegistered('schranz:search:reindex');
        $output = $this->runCommand('schranz:search:reindex', ['--drop' => true]);

        $this->assertStringContainsString('3/3', $output);
        $this->assertStringContainsString('Search indexes reindexed.', $output);
    }
}
