<?php

namespace Schranz\Search\SEAL\Adapter\Typesense;

use Schranz\Search\SEAL\Task\SyncTask;
use Typesense\Client;
use Schranz\Search\SEAL\Adapter\ConnectionInterface;
use Schranz\Search\SEAL\Marshaller\Marshaller;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Search\Condition;
use Schranz\Search\SEAL\Search\Result;
use Schranz\Search\SEAL\Search\Search;
use Schranz\Search\SEAL\Task\TaskInterface;
use Typesense\Exceptions\ObjectNotFound;
use Typesense\Exceptions\RequestMalformed;

final class TypesenseConnection implements ConnectionInterface
{
    private Marshaller $marshaller;

    public function __construct(
        private readonly Client $client,
    ) {
        $this->marshaller = new Marshaller(dateAsInteger: true);
    }

    public function save(Index $index, array $document, array $options = []): ?TaskInterface
    {
        $identifierField = $index->getIdentifierField();

        /** @var string|null $identifier */
        $identifier = ((string) $document[$identifierField->name]) ?? null;

        $marshalledDocument = $this->marshaller->marshall($index->fields, $document);
        $marshalledDocument['id'] = $identifier;

        $this->client->collections[$index->name]->documents->upsert($marshalledDocument);

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
    }

    public function delete(Index $index, string $identifier, array $options = []): ?TaskInterface
    {
        $this->client->collections[$index->name]->documents[$identifier]->delete();

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
            try {
                $data = $this->client->collections[$search->indexes[\array_key_first($search->indexes)]->name]->documents[$search->filters[0]->identifier]->retrieve();
            } catch (ObjectNotFound $e) {
                return new Result(
                    $this->hitsToDocuments($search->indexes, []),
                    0
                );
            }

            return new Result(
                $this->hitsToDocuments($search->indexes, [['document' => $data]]),
                1
            );
        }

        if (count($search->indexes) !== 1) {
            throw new \RuntimeException('Typesense does not yet support search multiple indexes: TODO');
        }

        $index = $search->indexes[\array_key_first($search->indexes)];


        $searchParams = [
            'q' => '',
            'query_by' => \implode(',', $index->searchableFields),
        ];

        $filters = [];
        foreach ($search->filters as $filter) {
            match (true) {
                $filter instanceof Condition\IdentifierCondition => $filters[] = 'id:=' . $filter->identifier, // TODO escape?
                $filter instanceof Condition\SearchCondition => $searchParams['q'] = $filter->query,
                $filter instanceof Condition\EqualCondition => $filters[] = $filter->field . ':=' . $filter->value, // TODO escape?
                $filter instanceof Condition\NotEqualCondition => $filters[] = $filter->field . ':!=' . $filter->value, // TODO escape?
                $filter instanceof Condition\GreaterThanCondition => $filters[] = $filter->field . ':>' . $filter->value, // TODO escape?
                $filter instanceof Condition\GreaterThanEqualCondition => $filters[] = $filter->field . ':>=' . $filter->value, // TODO escape?
                $filter instanceof Condition\LessThanCondition => $filters[] = $filter->field . ':<' . $filter->value, // TODO escape?
                $filter instanceof Condition\LessThanEqualCondition => $filters[] = $filter->field . ':<=' . $filter->value, // TODO escape?
                default => throw new \LogicException($filter::class . ' filter not implemented.'),
            };
        }

        if (\count($filters) !== 0) {
            $searchParams['filter_by'] = \implode(' && ', $filters);
        }

        if ($search->offset) {
            $searchParams['page'] = ($search->offset / $search->limit) + 1;
        }

        if ($search->limit) {
            $searchParams['per_page'] = $search->limit;
        }

        $sortBys = [];
        foreach ($search->sortBys as $field => $direction) {
            $sortBys[] = $field . ':' . $direction;
        }

        if (count($sortBys) > 0) {
            $searchParams['sort_by'] = \implode(',', $sortBys);
        }

        $data = $this->client->collections[$index->name]->documents->search($searchParams);

        return new Result(
            $this->hitsToDocuments($search->indexes, $data['hits']),
            $data['found'] ?? null,
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
            yield $this->marshaller->unmarshall($index->fields, $hit['document']);
        }
    }
}
