<?php

namespace Schranz\Search\SEAL\Adapter\ReadWrite;

use Schranz\Search\SEAL\Adapter\ConnectionInterface;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Search\Result;
use Schranz\Search\SEAL\Search\Search;

/**
 * @internal This class should never be needed to be instanced manually.
 */
final class ReadWriteConnection implements ConnectionInterface
{
    public function __construct(
        public readonly ConnectionInterface $readConnection,
        public readonly ConnectionInterface $writeConnection,
    ) {}

    public function save(Index $index, array $document): void
    {
        $this->writeConnection->save($index, $document);
    }

    public function delete(Index $index, string $identifier): void
    {
        $this->writeConnection->delete($index, $identifier);
    }

    public function search(Search $search): Result
    {
        return $this->readConnection->search($search);
    }
}
