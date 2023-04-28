<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use App\Tests\Support\UnitTester;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\CommandLoader\ContainerCommandLoader;
use Symfony\Component\Console\Tester\CommandTester;
use Yiisoft\Config\ConfigInterface;
use Yiisoft\Yii\Console\ExitCode;

use Yiisoft\Yii\Runner\Console\ConsoleApplicationRunner;

use function dirname;

final class HelloCest
{
    private ContainerInterface $container;
    private ConfigInterface $config;

    public function _before(UnitTester $I): void
    {
        $runner = new ConsoleApplicationRunner(
            rootPath: dirname(__DIR__, 2),
            debug: false,
            checkEvents: false,
        );
        $this->config = $runner->getConfig();
        $this->container = $runner->getContainer();
    }

    public function testExecute(UnitTester $I): void
    {
        $app = new Application();
        $params = $this->config->get('params-console');

        $loader = new ContainerCommandLoader(
            $this->container,
            $params['yiisoft/yii-console']['commands']
        );

        $app->setCommandLoader($loader);

        $command = $app->find('hello');

        $commandCreate = new CommandTester($command);

        $commandCreate->setInputs(['yes']);

        $I->assertSame(ExitCode::OK, $commandCreate->execute([]));

        $output = $commandCreate->getDisplay(true);

        $I->assertStringContainsString('Hello!', $output);
    }
}
