<?php

namespace Schranz\Search\SEAL\Adapter\Memory;

use Schranz\Search\SEAL\Adapter\ConnectionInterface;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Search\Condition\IdentifierCondition;
use Schranz\Search\SEAL\Search\Result;
use Schranz\Search\SEAL\Search\Search;

final class MemoryConnection implements ConnectionInterface
{
    public function save(Index $index, array $document): void
    {
        $document = MemoryStorage::save($index, $document);

        // return $document;
    }

    public function delete(Index $index, string $identifier): void
    {
        MemoryStorage::delete($index, $identifier);
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
