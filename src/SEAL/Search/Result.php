<?php

namespace Schranz\Search\SEAL\Search;

final class Result extends \IteratorIterator
{
    /**
     * @param \Generator<array<string, mixed>> $documents
     */
    public function __construct(
        \Generator $documents,
        readonly public int $total,
    ) {
        parent::__construct($documents);
    }
}
