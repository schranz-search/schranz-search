<?php

namespace Schranz\Search\SEAL\Adapter\Algolia;

use Algolia\AlgoliaSearch\Exceptions\NotFoundException;
use Algolia\AlgoliaSearch\SearchClient;
use Schranz\Search\SEAL\Adapter\ConnectionInterface;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Search\Condition\IdentifierCondition;
use Schranz\Search\SEAL\Search\Result;
use Schranz\Search\SEAL\Search\Search;
use Schranz\Search\SEAL\Task\SyncTask;
use Schranz\Search\SEAL\Task\TaskInterface;

final class AlgoliaConnection implements ConnectionInterface
{
    public function __construct(
        private readonly SearchClient $client,
    ) {
    }

    public function save(Index $index, array $document, array $options = []): ?TaskInterface
    {
        $identifierField = $index->getIdentifierField();

        $searchIndex = $this->client->initIndex($index->name);

        $searchIndex->saveObject($document, ['objectIDKey' => $identifierField->name]);

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask($document); // TODO wait for the result of the search engine
    }

    public function delete(Index $index, string $identifier, array $options = []): ?TaskInterface
    {
        $searchIndex = $this->client->initIndex($index->name);

        $searchIndex->deleteObject($identifier);

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null); // TODO wait for the result of the search engine
    }

    public function search(Search $search): Result
    {
        // optimized single document query
        if (
            count($search->indexes) === 1
            && count($search->filters) === 1
            && $search->filters[0] instanceof IdentifierCondition
            && ($search->offset === null || $search->offset === 0)
            && ($search->limit === null || $search->limit > 0)
        ) {
            $index = $search->indexes[\array_key_first($search->indexes)];
            $identifierField = $index->getIdentifierField();

            $searchIndex = $this->client->initIndex($index->name);

            try {
                $data = $searchIndex->getObject($search->filters[0]->identifier, ['objectIDKey' => $identifierField->name]);
            } catch (NotFoundException $e) {
                return new Result(
                    $this->hitsToDocuments($search->indexes, []),
                    0
                );
            }

            return new Result(
                $this->hitsToDocuments($search->indexes, [$data]),
                1
            );
        }

        if (count($search->indexes) !== 1) {
            throw new \RuntimeException('Algolia does not support multiple indexes in one query.');
        }

        $index = $this->client->initIndex($search->indexes[\array_key_first($search->indexes)]->name);

        $query = '';
        $filters = [];
        foreach ($search->filters as $filter) {
            if ($filter instanceof IdentifierCondition) {
                $filters[] = 'id:' . $filter->identifier; // TODO escape?
            } else {
                throw new \LogicException($filter::class . ' filter not implemented.');
            }
        }

        $searchParams = [];
        if (\count($filters) !== 0) {
            $searchParams = ['filters' => \implode(' AND ', $filters)];
        }

        $data = $index->search($query, $searchParams);

        return new Result(
            $this->hitsToDocuments($search->indexes, $data['hits']),
            $data['nbHits'] ?? null,
        );
    }

    /**
     * @param Index[] $indexes
     * @param iterable<array<string, mixed>> $hits
     *
     * @return \Generator<array<string, mixed>>
     */
    private function hitsToDocuments(array $indexes, iterable $hits): \Generator
    {
        foreach ($hits as $hit) {
            // remove Algolia Metadata
            unset($hit['objectID']);
            unset($hit['_highlightResult']);

            yield $hit;
        }
    }
}
