<?php

namespace Schranz\Search\SEAL\Search;

class Result extends \IteratorIterator
{
    /**
     * @param \Generator<array<string, mixed>> $documents
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
