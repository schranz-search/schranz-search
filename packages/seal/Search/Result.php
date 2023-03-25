<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Search;

/**
 * @extends \IteratorIterator<int, array<string, mixed>, \Generator>
 */
class Result extends \IteratorIterator
{
    /**
     * @param \Generator<int, array<string, mixed>> $documents
     */
    public function __construct(
        \Generator $documents,
        readonly private int $total,
    ) {
        parent::__construct($documents);
    }

    public function total(): int
    {
        return $this->total;
    }
}
