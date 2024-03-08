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

namespace Schranz\Search\Integration\Spiral\Console;

use Schranz\Search\SEAL\EngineRegistry;
use Schranz\Search\SEAL\Reindex\ReindexProviderInterface;
use Spiral\Console\Attribute\AsCommand;
use Spiral\Console\Attribute\Option;
use Spiral\Console\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * @experimental
 */
#[AsCommand(name: 'schranz:search:reindex', description: 'Reindex configured search indexes.')]
final class ReindexCommand extends Command
{
    #[Option(name: 'engine', mode: InputOption::VALUE_REQUIRED, description: 'The name of the engine')]
    private string|null $engineName = null;

    #[Option(name: 'index', mode: InputOption::VALUE_REQUIRED, description: 'The name of the index')]
    private string|null $indexName = null;

    #[Option(shortcut: 'd', description: 'Drop the index before reindexing.')]
    private bool $drop = false;

    /**
     * @param iterable<ReindexProviderInterface> $reindexProviders
     */
    public function __construct(
        private readonly iterable $reindexProviders, // TODO move to __invoke method
    ) {
        parent::__construct();
    }

    public function __invoke(
        EngineRegistry $engineRegistry,
    ): int {
        foreach ($engineRegistry->getEngines() as $name => $engine) {
            if ($this->engineName && $this->engineName !== $name) {
                continue;
            }

            $this->line('Engine: ' . $name);

            $progressBar = $this->output->createProgressBar();

            $engine->reindex(
                $this->reindexProviders,
                $this->indexName,
                $this->drop,
                function (string $index, int $count, int|null $total) use ($progressBar) {
                    $progressBar->setMessage($index);
                    $progressBar->setProgress($count);

                    if (null !== $total) {
                        $progressBar->setMaxSteps($total);
                    }
                },
            );

            $progressBar->finish();

            $this->line('');
            $this->line('');
        }

        $this->info('Search indexes reindexed.');

        return self::SUCCESS;
    }
}
