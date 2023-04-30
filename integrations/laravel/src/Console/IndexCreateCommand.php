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

namespace Schranz\Search\Integration\Laravel\Console;

use Illuminate\Console\Command;
use Schranz\Search\SEAL\EngineRegistry;

/**
 * @experimental
 */
final class IndexCreateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schranz:search:index-create {--engine= : The name of the engine} {--index= : The name of the index}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create configured search indexes.';

    /**
     * Execute the console command.
     */
    public function handle(EngineRegistry $engineRegistry): int
    {
        /** @var string|null $engineName */
        $engineName = $this->option('engine');
        /** @var string|null $indexName */
        $indexName = $this->option('index');

        foreach ($engineRegistry->getEngines() as $name => $engine) {
            if ($engineName && $engineName !== $name) {
                continue;
            }

            if ($indexName) {
                $this->line('Creating search index "' . $indexName . '" of "' . $name . '" ...');
                $engine->createIndex($indexName);

                continue;
            }

            $this->line('Creating search indexes of "' . $name . '" ...');
            $engine->createSchema();
        }

        $this->info('Search indexes created.');

        return 0;
    }
}
