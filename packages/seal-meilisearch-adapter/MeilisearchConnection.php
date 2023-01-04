<?php

namespace Schranz\Search\SEAL\Adapter\Meilisearch;

use Meilisearch\Client;
use Meilisearch\Exceptions\ApiException;
use Schranz\Search\SEAL\Adapter\ConnectionInterface;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Search\Condition\IdentifierCondition;
use Schranz\Search\SEAL\Search\Result;
use Schranz\Search\SEAL\Search\Search;

final class MeilisearchConnection implements ConnectionInterface
{
    public function __construct(
        private readonly Client $client,
    ) {
    }

    public function save(Index $index, array $document): void
    {
        $identifierField = $index->getIdentifierField();

        /** @var string|null $identifier */
        $identifier = ((string) $document[$identifierField->name]) ?? null;

        $data = $this->client->index($index->name)->addDocuments([$document], $identifierField->name);

        if ($data['status'] !== 'enqueued') {
            throw new \RuntimeException('Unexpected error while save document with identifier "' . $identifier . '" into Index "' . $index->name . '".');
        }

        // return $document;
    }

    public function delete(Index $index, string $identifier): void
    {
        $data = $this->client->index($index->name)->deleteDocument($identifier);

        if ($data['status'] !== 'enqueued') {
            throw new \RuntimeException('Unexpected error while delete document with identifier "' . $identifier . '" from Index "' . $index->name . '".');
        }
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
            try {
                $data = $this->client->index($search->indexes[\array_key_first($search->indexes)]->name)->getDocument($search->filters[0]->identifier);
            } catch (ApiException $e) {
                if ($e->httpStatus !== 404) {
                    throw $e;
                }

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
            throw new \RuntimeException('Meilisearch does not support multiple indexes in one query.');
        }

        $index = $this->client->index($search->indexes[\array_key_first($search->indexes)]->name);

        $query = null;
        $filters = [];
        foreach ($search->filters as $filter) {
            if ($filter instanceof IdentifierCondition) {
                $filters[] = 'id = "' . $filter->identifier . '"'; // TODO escape?
            } else {
                throw new \LogicException($filter::class . ' filter not implemented.');
            }
        }

        $searchParams = [];
        if (\count($filters) !== 0) {
            $searchParams = ['filter' => \implode(' AND ', $filters)];
        }

        $data = $index->search($query, $searchParams)->toArray();

        return new Result(
            $this->hitsToDocuments($search->indexes, $data['hits']),
            $data['totalHits'] ?? $data['estimatedTotalHits'] ?? null,
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
            yield $hit;
        }
    }
}
