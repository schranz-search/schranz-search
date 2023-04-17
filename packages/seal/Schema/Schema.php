<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Schema;

/**
 * @readonly
 */
final class Schema
{
    /**
     * @param array<string, Index> $indexes
     */
    public function __construct(
        public readonly array $indexes,
    ) {
    }
}
