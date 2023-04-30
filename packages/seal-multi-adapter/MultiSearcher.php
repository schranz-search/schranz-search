<?php

declare(strict_types=1);

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
