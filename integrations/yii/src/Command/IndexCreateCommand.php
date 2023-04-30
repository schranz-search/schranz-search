<?php

declare(strict_types=1);

namespace Schranz\Search\Integration\Yii\Command;

use Schranz\Search\SEAL\EngineRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @experimental
 */
final class IndexCreateCommand extends Command
{
    public function __construct(private readonly EngineRegistry $engineRegistry)
    {
        parent::__construct('schranz:search:index-create');
    }

    protected function configure()
    {
        $this->setDescription('Create configured search indexes.');
        $this->addOption('engine', null, InputOption::VALUE_REQUIRED, 'The name of the engine to create the schema for.');
        $this->addOption('index', null, InputOption::VALUE_REQUIRED, 'The name of the index to create the schema for.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $ui = new SymfonyStyle($input, $output);
        /** @var string|null $engineName */
        $engineName = $input->getOption('engine');
        /** @var string|null $indexName */
        $indexName = $input->getOption('index');

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
