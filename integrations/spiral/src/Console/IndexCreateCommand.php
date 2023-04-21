<?php

declare(strict_types=1);

namespace Schranz\Search\Integration\Spiral\Console;

use Schranz\Search\SEAL\EngineRegistry;
use Spiral\Console\Attribute\Argument;
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
    #[Argument(name: 'engine', description: 'The name of the engine')]
    private string|null $engineName = null;

    #[Argument(name: 'index', description: 'The name of the index')]
    private string|null $indexName = null;

    public function __invoke(EngineRegistry $engineRegistry): int
    {
        foreach ($engineRegistry->getEngines() as $name => $engine) {
            if ($this->engineName && $this->engineName !== $name) {
                continue;
            }

            if ($this->indexName) {
                $this->line(sprintf('Creating search index "%s" of "%s" ...', $this->indexName, $name));
                $engine->createIndex($this->indexName);

                continue;
            }

            $this->line(sprintf('Creating search indexes of "%s" ...', $name));
            $engine->createSchema();
        }

        $this->info('Search indexes created.');

        return self::SUCCESS;
    }
}
