<?php

declare(strict_types=1);

namespace Schranz\Search\Integration\Mezzio\Command;

use Schranz\Search\SEAL\EngineRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @experimental
 */
final class IndexDropCommand extends Command
{
    public function __construct(private readonly EngineRegistry $engineRegistry)
    {
        parent::__construct('schranz:search:index-drop');
    }

    protected function configure()
    {
        $this->setDescription('Drop configured search indexes.');
        $this->addOption('engine', null, InputOption::VALUE_REQUIRED, 'The name of the engine to create the schema for.');
        $this->addOption('index', null, InputOption::VALUE_REQUIRED, 'The name of the index to create the schema for.');
        $this->addOption('force', 'f', InputOption::VALUE_NONE, 'Without force nothing will happen in this command.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $ui = new SymfonyStyle($input, $output);
        /** @var string|null $engineName */
        $engineName = $input->getOption('engine');
        /** @var string|null $indexName */
        $indexName = $input->getOption('index');
        $force = $input->getOption('force');

        if (!$force) {
            $ui->error('You need to use the --force option to drop the search indexes.');

            return Command::FAILURE;
        }

        foreach ($this->engineRegistry->getEngines() as $name => $engine) {
            if ($engineName && $engineName !== $name) {
                continue;
            }

            if ($indexName) {
                $ui->text('Dropping search index "' . $indexName . '" for "' . $name . '" ...');
                $engine->dropIndex($indexName);

                continue;
            }

            $ui->text('Dropping search indexes of "' . $name . '" ...');
            $engine->dropSchema();
        }

        $ui->success('Search indexes dropped.');

        return Command::SUCCESS;
    }
}
