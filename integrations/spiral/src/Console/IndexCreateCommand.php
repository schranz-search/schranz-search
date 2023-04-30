<?php

declare(strict_types=1);

namespace Schranz\Search\Integration\Spiral\Console;

use Schranz\Search\SEAL\EngineRegistry;
use Spiral\Console\Attribute\Option;
use Symfony\Component\Console\Input\InputOption;
use Spiral\Console\Attribute\AsCommand;
use Spiral\Console\Command;

/**
 * @experimental
 */
#[AsCommand(
    name: 'schranz:search:index-create',
    description: 'Create configured search indexes.',
)]
final class IndexCreateCommand extends Command
{
    #[Option(name: 'engine', mode: InputOption::VALUE_REQUIRED, description: 'The name of the engine')]
    private string|null $engineName = null;

    #[Option(name: 'index', mode: InputOption::VALUE_REQUIRED, description: 'The name of the index')]
    private string|null $indexName = null;

    public function __invoke(EngineRegistry $engineRegistry): int
    {
        foreach ($engineRegistry->getEngines() as $name => $engine) {
            if ($this->engineName && $this->engineName !== $name) {
                continue;
            }

            if ($this->indexName) {
                $this->line('Creating search index "' . $this->indexName . '" of "' . $name . '" ...');
                $engine->createIndex($this->indexName);

                continue;
            }

            $this->line('Creating search indexes of "' . $name . '" ...');
            $engine->createSchema();
        }

        $this->info('Search indexes created.');

        return self::SUCCESS;
    }
}
