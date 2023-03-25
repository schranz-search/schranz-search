<?php

namespace Schranz\Search\SEAL\Adapter\ReadWrite;

use Schranz\Search\SEAL\Adapter\IndexerInterface;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Task\SyncTask;
use Schranz\Search\SEAL\Task\TaskInterface;

/**
 * @internal This class should never be needed to be instanced manually.
 */
final class MultiIndexer implements IndexerInterface
{
    /**
     * @param iterable<IndexerInterface> $indexers
     */
    public function __construct(
        public readonly iterable $indexers,
    ) {}

    public function save(Index $index, array $document, array $options = []): ?TaskInterface
    {
        $document = null;
        foreach ($this->indexers as $indexer) {
            $document = $indexer->save($index, $document);
        }

        if ($document === null) {
            throw new \LogicException('No connections were available.');
        }

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask($document);
    }

    public function delete(Index $index, string $identifier, array $options = []): ?TaskInterface
    {
        foreach ($this->indexers as $indexer) {
            $indexer->delete($index, $identifier);
        }

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
    }
}
