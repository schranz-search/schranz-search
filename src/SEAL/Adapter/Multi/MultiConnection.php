<?php

namespace Schranz\Search\SEAL\Adapter\ReadWrite;

use Schranz\Search\SEAL\Adapter\ConnectionInterface;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\DSL\Result;
use Schranz\Search\SEAL\DSL\Search;

/**
 * @internal This class should never be needed to be instanced manually.
 */
final class MultiConnection implements ConnectionInterface
{
    /**
     * @param iterable<ConnectionInterface> $connections
     */
    public function __construct(
        public readonly iterable $connections,
    ) {}

    public function save(Index $index, array $document): array
    {
        $document = null;
        foreach ($this->connections as $connection) {
            $document = $connection->save($index, $document);
        }

        if ($document === null) {
            throw new \LogicException('No connections were available.');
        }

        return $document;
    }

    public function delete(Index $index, string $identifier): void
    {
        foreach ($this->connections as $connection) {
            $connection->delete($index, $identifier);
        }
    }

    public function search(Search $search): Result
    {
        throw new \LogicException(
            'Not implemented yet, use the ReadWriteAdapter to define a specific read adapter.'
        );
    }
}
