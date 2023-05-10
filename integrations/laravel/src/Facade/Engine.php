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

namespace Schranz\Search\Integration\Laravel\Facade;

use Illuminate\Support\Facades\Facade;
use Schranz\Search\SEAL\EngineInterface;
use Schranz\Search\SEAL\Search\SearchBuilder;

/**
 * @method static void saveDocument(string $index, array $document)
 * @method static void deleteDocument(string $index, string $identifier)
 * @method static array<string, mixed> getDocument(string $index, string $identifier)
 * @method static SearchBuilder createSearchBuilder()
 * @method static void createIndex(string $index)
 * @method static void dropIndex(string $index)
 * @method static bool existIndex(string $index)
 * @method static void createSchema()
 * @method static void dropSchema()
 * @method static void reindex(iterable $reindexProviders, string|null $index = null, bool $dropIndex = false, callable $progressCallback = null)
 *
 * @see \Schranz\Search\SEAL\EngineInterface
 */
class Engine extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return EngineInterface::class;
    }
}
