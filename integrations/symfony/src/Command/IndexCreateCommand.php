<?php

declare(strict_types=1);

namespace Schranz\Search\Integration\Symfony\Command;

use Schranz\Search\SEAL\EngineRegistry;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @experimental
 */
#[AsCommand(name: 'schranz:search:index-create', description: 'Create configured search indexes.')]
final class IndexCreateCommand extends Command
{
    public function __construct(private readonly EngineRegistry $engineRegistry)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument('engine', InputArgument::OPTIONAL, 'The name of the engine to create the schema for.');
        $this->addArgument('index', InputArgument::OPTIONAL, 'The name of the index to create the schema for.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $ui = new SymfonyStyle($input, $output);
        /** @var string|null $engineName */
        $engineName = $input->getArgument('engine');
        /** @var string|null $indexName */
        $indexName = $input->getArgument('index');

        foreach ($this->engineRegistry->getEngines() as $name => $engine) {
            if ($engineName && $engineName !== $name) {
                continue;
            }

            if ($indexName) {
                $ui->text('Creating search index "' . $indexName . '" of "' . $name . '" ...');
                $engine->createIndex($indexName);

                continue;
            }

            $ui->text('Creating search indexes of "' . $name . '" ...');
            $engine->createSchema();
        }

        $ui->success('Search indexes created.');

        return Command::SUCCESS;
    }
}
