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
use Schranz\Search\SEAL\EngineRegistry as SealEngineRegistry;

/**
 * @method static iterable<string, EngineInterface> getEngines()
 * @method static EngineInterface getEngine(string $name)
 *
 * @see \Schranz\Search\SEAL\EngineRegistry
 */
class EngineRegistry extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SealEngineRegistry::class;
    }
}
