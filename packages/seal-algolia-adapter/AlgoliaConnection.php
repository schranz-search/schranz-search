<?php

namespace Schranz\Search\SEAL\Adapter\Algolia;

use Algolia\AlgoliaSearch\Exceptions\NotFoundException;
use Algolia\AlgoliaSearch\SearchClient;
use Schranz\Search\SEAL\Adapter\ConnectionInterface;
use Schranz\Search\SEAL\Marshaller\Marshaller;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Search\Condition;
use Schranz\Search\SEAL\Search\Result;
use Schranz\Search\SEAL\Search\Search;
use Schranz\Search\SEAL\Task\AsyncTask;
use Schranz\Search\SEAL\Task\TaskInterface;

final class AlgoliaConnection implements ConnectionInterface
{
    private Marshaller $marshaller;

    public function __construct(
        private readonly SearchClient $client,
    ) {
        $this->marshaller = new Marshaller();
    }

    public function save(Index $index, array $document, array $options = []): ?TaskInterface
    {
        $identifierField = $index->getIdentifierField();

        $searchIndex = $this->client->initIndex($index->name);

        $batchIndexingResponse = $searchIndex->saveObject(
            $this->marshaller->marshall($index->fields, $document),
            ['objectIDKey' => $identifierField->name]
        );

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new AsyncTask(function() use ($batchIndexingResponse, $document) {
            $batchIndexingResponse->wait();

            return $document;
        });
    }

    public function delete(Index $index, string $identifier, array $options = []): ?TaskInterface
    {
        $searchIndex = $this->client->initIndex($index->name);

        $batchIndexingResponse = $searchIndex->deleteObject($identifier);

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new AsyncTask(function() use ($batchIndexingResponse) {
            $batchIndexingResponse->wait();
        });
    }

    public function search(Search $search): Result
    {
        // optimized single document query
        if (
            count($search->indexes) === 1
            && count($search->filters) === 1
            && $search->filters[0] instanceof Condition\IdentifierCondition
            && $search->offset === 0
            && $search->limit === 1
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
            throw new \RuntimeException('Algolia Adapter does not yet support search multiple indexes: https://github.com/schranz-search/schranz-search/issues/41');
        }

        if (count($search->sortBys) > 1) {
            throw new \RuntimeException('Algolia Adapter does not yet support search multiple indexes: https://github.com/schranz-search/schranz-search/issues/41');
        }

        $index = $search->indexes[\array_key_first($search->indexes)];
        $indexName = $index->name;

        $sortByField = \array_key_first($search->sortBys);
        if ($sortByField) {
            $indexName .= '__' . \str_replace('.', '_', $sortByField) . '_' . $search->sortBys[$sortByField];
        }

        $searchIndex = $this->client->initIndex($indexName);

        $query = '';
        $filters = [];
        foreach ($search->filters as $filter) {
            match (true) {
                $filter instanceof Condition\IdentifierCondition => $filters[] = $index->getIdentifierField()->name . ':' . $filter->identifier,
                $filter instanceof Condition\SearchCondition => $query = $filter->query,
                $filter instanceof Condition\EqualCondition => $filters[] = $filter->field . ':' . $filter->value,
                $filter instanceof Condition\NotEqualCondition => $filters[] = 'NOT ' . $filter->field . ':' . $filter->value,
                $filter instanceof Condition\GreaterThanCondition => $filters[] = $filter->field . ' > ' . $filter->value, // TODO escape?
                $filter instanceof Condition\GreaterThanEqualCondition => $filters[] = $filter->field . ' >= ' . $filter->value, // TODO escape?
                $filter instanceof Condition\LessThanCondition => $filters[] = $filter->field . ' < ' . $filter->value, // TODO escape?
                $filter instanceof Condition\LessThanEqualCondition => $filters[] = $filter->field . ' <= ' . $filter->value, // TODO escape?
                default =>  throw new \LogicException($filter::class . ' filter not implemented.'),
            };
        }

        $searchParams = [];
        if (\count($filters) !== 0) {
            $searchParams = ['filters' => \implode(' AND ', $filters)];
        }

        if ($search->offset) {
            $searchParams['offset'] = $search->offset;
        }

        if ($search->limit) {
            $searchParams['length'] = $search->limit;
            $searchParams['offset'] = $searchParams['offset'] ?? 0; // length would be ignored without offset see: https://www.algolia.com/doc/api-reference/api-parameters/length/
        }

        $data = $searchIndex->search($query, $searchParams);

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
        $index = $indexes[\array_key_first($indexes)];

        foreach ($hits as $hit) {
            // remove Algolia Metadata
            unset($hit['objectID']);
            unset($hit['_highlightResult']);

            yield $this->marshaller->unmarshall($index->fields, $hit);
        }
    }
}
