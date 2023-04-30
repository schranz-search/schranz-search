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

namespace Schranz\Search\SEAL\Adapter\Multi;

use Schranz\Search\SEAL\Adapter\SearcherInterface;
use Schranz\Search\SEAL\Search\Search;

/**
 * @internal this class should never be needed to be instanced manually
 */
final class MultiSearcher implements SearcherInterface
{
    /**
     * @param iterable<SearcherInterface> $searchers
     */
    public function __construct(
        public readonly iterable $searchers,
    ) {
    }

    public function search(Search $search): never
    {
        throw new \LogicException(
            'Not implemented yet, use the ReadWriteAdapter to define a specific read adapter.',
        );
    }
}
