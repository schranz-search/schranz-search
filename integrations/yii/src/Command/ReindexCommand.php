<?php

declare(strict_types=1);

/*
 * This file is part of the Schranz Search package.
 *
 * (c) Alexander Schranz <alexander@sulu.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Schranz\Search\Integration\Yii\Command;

use Schranz\Search\SEAL\EngineRegistry;
use Schranz\Search\SEAL\Reindex\ReindexProviderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @experimental
 */
final class ReindexCommand extends Command
{
    /**
     * @param iterable<ReindexProviderInterface> $reindexProviders
     */
    public function __construct(
        private readonly EngineRegistry $engineRegistry,
        private readonly iterable $reindexProviders,
    ) {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Reindex configured search indexes.');
        $this->addOption('engine', null, InputOption::VALUE_REQUIRED, 'The name of the engine to create the schema for.');
        $this->addOption('index', null, InputOption::VALUE_REQUIRED, 'The name of the index to create the schema for.');
        $this->addOption('drop', null, InputOption::VALUE_NONE, 'Drop the index before reindexing.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $ui = new SymfonyStyle($input, $output);
        /** @var string|null $engineName */
        $engineName = $input->getOption('engine');
        /** @var string|null $indexName */
        $indexName = $input->getOption('index');
        /** @var bool $drop */
        $drop = $input->getOption('drop');

        foreach ($this->engineRegistry->getEngines() as $name => $engine) {
            if ($engineName && $engineName !== $name) {
                continue;
            }

            $ui->section('Engine: ' . $name);

            $progressBar = $ui->createProgressBar();

            $engine->reindex(
                $this->reindexProviders,
                $indexName,
                $drop,
                function (string $index, int $count, ?int $total) use ($progressBar) {
                    $progressBar->setMessage($index);
                    $progressBar->setProgress($count);

                    if (null !== $total) {
                        $progressBar->setMaxSteps($total);
                    }
                },
            );

            $progressBar->finish();
            $ui->writeln('');
            $ui->writeln('');
        }

        $ui->success('Search indexes reindexed.');

        return Command::SUCCESS;
    }
}
