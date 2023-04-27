<?php

declare(strict_types=1);

namespace AppTest\Command;

use AppTest\FunctionalTestCase;
use Schranz\Search\Integration\Mezzio\Command\IndexCreateCommand;
use Schranz\Search\Integration\Mezzio\Command\IndexDropCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

final class CommandTest extends FunctionalTestCase
{
    public function testCreate(): void
    {
        /** @var IndexCreateCommand $indexCreateCommand */
        $indexCreateCommand = $this->container->get(IndexCreateCommand::class);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();

        $this->assertSame(0, $indexCreateCommand->run($input, $output));

        $this->assertStringContainsString('Search indexes created.', $output->fetch());
    }

    public function testDrop(): void
    {
        /** @var IndexDropCommand $command */
        $command = $this->container->get(IndexDropCommand::class);

        $input = new ArrayInput([
            '--force' => true,
        ]);
        $output = new BufferedOutput();

        $this->assertSame(0, $command->run($input, $output));

        $this->assertStringContainsString('Search indexes dropped.', $output->fetch());
    }
}
