<?php

namespace Schranz\Search\SEAL\Adapter\Memory;

use Schranz\Search\SEAL\Adapter\ConnectionInterface;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Search\Condition\IdentifierCondition;
use Schranz\Search\SEAL\Search\Result;
use Schranz\Search\SEAL\Search\Search;
use Schranz\Search\SEAL\Task\SyncTask;
use Schranz\Search\SEAL\Task\TaskInterface;

final class MemoryConnection implements ConnectionInterface
{
    public function save(Index $index, array $document, array $options = []): ?TaskInterface
    {
        $document = MemoryStorage::save($index, $document);

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask($document);
    }

    public function delete(Index $index, string $identifier, array $options = []): ?TaskInterface
    {
        MemoryStorage::delete($index, $identifier);

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
    }

    public function search(Search $search): Result
    {
        $documents = [];

        foreach ($search->indexes as $index) {
            foreach (MemoryStorage::getDocuments($index) as $identifier => $document) {
                $identifier = (string) $identifier;

                if (count($search->filters) === 0) {
                    $documents[] = $document;

                    continue;
                }

                foreach ($search->filters as $filter) {
                    if ($filter instanceof IdentifierCondition) {
                        if ($filter->identifier !== $identifier) {
                            continue 2;
                        }
                    } else {
                        throw new \LogicException($filter::class . ' filter not implemented.');
                    }
                }

                $documents[] = $document;
            }
        }

        $generator = (function() use ($documents): \Generator {
            foreach ($documents as $document) {
                yield $document;
            }
        });

        return new Result(
            $generator(),
            count($documents),
        );
    }
}
