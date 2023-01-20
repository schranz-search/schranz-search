<?php

namespace Schranz\Search\SEAL\Adapter\Solr;

use Schranz\Search\SEAL\Task\SyncTask;
use Solarium\Client;
use Schranz\Search\SEAL\Adapter\ConnectionInterface;
use Schranz\Search\SEAL\Marshaller\Marshaller;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Search\Condition;
use Schranz\Search\SEAL\Search\Result;
use Schranz\Search\SEAL\Search\Search;
use Schranz\Search\SEAL\Task\AsyncTask;
use Schranz\Search\SEAL\Task\TaskInterface;

final class SolrConnection implements ConnectionInterface
{
    private Marshaller $marshaller;

    public function __construct(
        private readonly Client $client,
    ) {
        $this->marshaller = new Marshaller();
    }

    public function save(Index $index, array $document, array $options = []): ?TaskInterface
    {
        $identifierField = $index->getIdentifierField();

        /** @var string|null $identifier */
        $identifier = ((string) $document[$identifierField->name]) ?? null;

        $marshalledDocument = $this->marshaller->marshall($index->fields, $document);
        $marshalledDocument['id'] = $identifier;

        $update = $this->client->createUpdate();
        $indexDocument = $update->createDocument();

        $indexDocument->id = $identifier;

        $update->addDocuments([$indexDocument]);
        $update->addCommit();

        $this->client->getEndpoint()
            ->setCollection($index->name);

        $this->client->update($update);

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
    }

    public function delete(Index $index, string $identifier, array $options = []): ?TaskInterface
    {
        $update = $this->client->createUpdate();
        $query = $update->addDeleteById($identifier);

        $update->addDeleteQuery($query);
        $update->addCommit();

        $this->client->getEndpoint()
            ->setCollection($index->name);

        $this->client->update($update);

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
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
            $this->client->getEndpoint()
                ->setCollection($search->indexes[\array_key_first($search->indexes)]->name);

            $query = $this->client->createRealtimeGet();
            $query->addId($search->filters[0]->identifier);
            $result = $this->client->realtimeGet($query);

            if (!$result->getNumFound()) {
                return new Result(
                    $this->hitsToDocuments($search->indexes, []),
                    0
                );
            }

            return new Result(
                $this->hitsToDocuments($search->indexes, [$result->getDocument()->getFields()]),
                1
            );
        }

        if (count($search->indexes) !== 1) {
            throw new \RuntimeException('Solr does not yet support search multiple indexes: https://github.com/schranz-search/schranz-search/issues/28');
        }

        $index = $search->indexes[\array_key_first($search->indexes)];
        $searchIndex = $this->client->index($index->name);

        $query = null;
        $filters = [];
        foreach ($search->filters as $filter) {
            match (true) {
                $filter instanceof Condition\IdentifierCondition => $filters[] = $index->getIdentifierField()->name . ' = "' . $filter->identifier . '"', // TODO escape?
                $filter instanceof Condition\SearchCondition => $query = $filter->query,
                $filter instanceof Condition\EqualCondition => $filters[] = $filter->field . ' = ' . $filter->value, // TODO escape?
                $filter instanceof Condition\NotEqualCondition => $filters[] = $filter->field . ' != ' . $filter->value, // TODO escape?
                $filter instanceof Condition\GreaterThanCondition => $filters[] = $filter->field . ' > ' . $filter->value, // TODO escape?
                $filter instanceof Condition\GreaterThanEqualCondition => $filters[] = $filter->field . ' >= ' . $filter->value, // TODO escape?
                $filter instanceof Condition\LessThanCondition => $filters[] = $filter->field . ' < ' . $filter->value, // TODO escape?
                $filter instanceof Condition\LessThanEqualCondition => $filters[] = $filter->field . ' <= ' . $filter->value, // TODO escape?
                default => throw new \LogicException($filter::class . ' filter not implemented.'),
            };
        }

        $searchParams = [];
        if (\count($filters) !== 0) {
            $searchParams = ['filter' => \implode(' AND ', $filters)];
        }

        if ($search->offset) {
            $searchParams['offset'] = $search->offset;
        }

        if ($search->limit) {
            $searchParams['limit'] = $search->limit;
        }

        foreach ($search->sortBys as $field => $direction) {
            $searchParams['sort'][] = $field . ':' . $direction;
        }

        $data = $searchIndex->search($query, $searchParams)->toArray();

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
        $index = $indexes[\array_key_first($indexes)];

        foreach ($hits as $hit) {
            unset($hit['_version_']);

            if ($index->getIdentifierField()->name !== 'id') {
                $id = $hit['id'];
                unset($hit['id']);

                $hit[$index->getIdentifierField()->name] = $id;
            }

            yield $this->marshaller->unmarshall($index->fields, $hit);
        }
    }
}
