<?php

declare(strict_types=1);

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class CommandTest extends KernelTestCase
{
    public function testCreate(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('schranz:search:index-create');
        $commandTester = new CommandTester($command);
        $commandTester->execute([]);

        $commandTester->assertCommandIsSuccessful();
    }

    public function testDrop(): void
    {
        $kernel = self::bootKernel();
        $application = new Application($kernel);

        $command = $application->find('schranz:search:index-drop');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            '--force' => true,
        ]);

        $commandTester->assertCommandIsSuccessful();
    }
}
