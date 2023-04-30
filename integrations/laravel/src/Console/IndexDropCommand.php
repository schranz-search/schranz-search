<?php

declare(strict_types=1);

namespace Schranz\Search\Integration\Laravel\Console;

use Illuminate\Console\Command;
use Schranz\Search\SEAL\EngineRegistry;

/**
 * @experimental
 */
final class IndexDropCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schranz:search:index-drop {--engine= : The name of the engine} {--index= : The name of the index} {--force : Force to drop the indexes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop configured search indexes.';

    /**
     * Execute the console command.
     */
    public function handle(EngineRegistry $engineRegistry): int
    {
        /** @var string|null $engineName */
        $engineName = $this->option('engine');
        /** @var string|null $indexName */
        $indexName = $this->option('index');
        /** @var bool $force */
        $force = $this->option('force') ?: false;

        if (!$force) {
            $this->error('You need to use the --force option to drop the search indexes.');

            return 1;
        }

        foreach ($engineRegistry->getEngines() as $name => $engine) {
            if ($engineName && $engineName !== $name) {
                continue;
            }

            if ($indexName && $indexName === $name) {
                $this->line('Drop search index "' . $indexName . '" of "' . $name . '" ...');
                $engine->dropIndex($indexName);

                continue;
            }

            $this->line('Drop search indexes of "' . $name . '" ...');
            $engine->dropSchema();
        }

        $this->info('Search indexes created.');

        return 0;
    }
}
