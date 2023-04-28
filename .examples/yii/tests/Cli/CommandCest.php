<?php

declare(strict_types=1);

namespace App\Tests\Cli;

use App\Tests\Support\CliTester;

final class CommandCest
{
    public function testCreate(CliTester $I): void
    {
        $command = \dirname(__DIR__, 2) . '/yii';
        $I->runShellCommand($command . ' schranz:search:index-create');
        $I->seeInShellOutput('Search indexes created.');
    }

    public function testDrop(CliTester $I): void
    {
        $command = \dirname(__DIR__, 2) . '/yii';
        $I->runShellCommand($command . ' schranz:search:index-drop --force');
        $I->seeInShellOutput('Search indexes dropped.');
    }
}
