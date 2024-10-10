<?php

declare(strict_types=1);

/*
 * This file is part of the Schranz Search package.
 *
 * (c) Alexander Schranz <alexander@sulu.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Schranz\Search\SEAL\Adapter\Typesense;

use Schranz\Search\SEAL\Adapter\SearcherInterface;
use Schranz\Search\SEAL\Marshaller\Marshaller;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Search\Condition;
use Schranz\Search\SEAL\Search\Result;
use Schranz\Search\SEAL\Search\Search;
use Typesense\Client;
use Typesense\Exceptions\ObjectNotFound;

final class TypesenseSearcher implements SearcherInterface
{
    private readonly Marshaller $marshaller;

    public function __construct(
        private readonly Client $client,
    ) {
        $this->marshaller = new Marshaller(
            dateAsInteger: true,
            geoPointFieldConfig: [
                'latitude' => 0,
                'longitude' => 1,
            ],
        );
    }

    public function search(Search $search): Result
    {
        // optimized single document query
        if (
            1 === \count($search->indexes)
            && 1 === \count($search->filters)
            && $search->filters[0] instanceof Condition\IdentifierCondition
            && 0 === $search->offset
            && 1 === $search->limit
        ) {
            try {
                $data = $this->client->collections[$search->indexes[\array_key_first($search->indexes)]->name]->documents[$search->filters[0]->identifier]->retrieve();
            } catch (ObjectNotFound) {
                return new Result(
                    $this->hitsToDocuments($search->indexes, []),
                    0,
                );
            }

            return new Result(
                $this->hitsToDocuments($search->indexes, [['document' => $data]]),
                1,
            );
        }

        if (1 !== \count($search->indexes)) {
            throw new \RuntimeException('Typesense does not yet support search multiple indexes: TODO');
        }

        $index = $search->indexes[\array_key_first($search->indexes)];

        $searchParams = [
            'q' => '',
            'query_by' => \implode(',', $index->searchableFields),
        ];

        $query = null;
        $filters = $this->recursiveResolveFilterConditions($index, $search->filters, true, $query);

        if (null !== $query) {
            $searchParams['q'] = $query;
        }

        if ('' !== $filters) {
            $searchParams['filter_by'] = $filters;
        }

        if (0 !== $search->offset) {
            $searchParams['page'] = ($search->offset / $search->limit) + 1;
        }

        if ($search->limit) {
            $searchParams['per_page'] = $search->limit;
        }

        $sortBys = [];
        foreach ($search->sortBys as $field => $direction) {
            $sortBys[] = $field . ':' . $direction;
        }

        if ([] !== $sortBys) {
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
     * @return \Generator<int, array<string, mixed>>
     */
    private function hitsToDocuments(array $indexes, iterable $hits): \Generator
    {
        $index = $indexes[\array_key_first($indexes)];

        /** @var array{document: array<string, mixed>} $hit */
        foreach ($hits as $hit) {
            yield $this->marshaller->unmarshall($index->fields, $hit['document']);
        }
    }

    private function escapeFilterValue(string|int|float|bool $value): string
    {
        return match (true) {
            \is_string($value) => '"' . \addcslashes($value, '"&') . '"', // TODO escape?
            \is_bool($value) => $value ? 'true' : 'false',
            default => (string) $value,
        };
    }

    /**
     * @param object[] $conditions
     */
    private function recursiveResolveFilterConditions(Index $index, array $conditions, bool $conjunctive, string|null &$query): string
    {
        $filters = [];

        foreach ($conditions as $filter) {
            match (true) {
                $filter instanceof Condition\IdentifierCondition => $filters[] = 'id:=' . $this->escapeFilterValue($filter->identifier),
                $filter instanceof Condition\SearchCondition => $query = $filter->query,
                $filter instanceof Condition\EqualCondition => $filters[] = $filter->field . ':=' . $this->escapeFilterValue($filter->value),
                $filter instanceof Condition\NotEqualCondition => $filters[] = $filter->field . ':!=' . $this->escapeFilterValue($filter->value),
                $filter instanceof Condition\GreaterThanCondition => $filters[] = $filter->field . ':>' . $this->escapeFilterValue($filter->value),
                $filter instanceof Condition\GreaterThanEqualCondition => $filters[] = $filter->field . ':>=' . $this->escapeFilterValue($filter->value),
                $filter instanceof Condition\LessThanCondition => $filters[] = $filter->field . ':<' . $this->escapeFilterValue($filter->value),
                $filter instanceof Condition\LessThanEqualCondition => $filters[] = $filter->field . ':<=' . $this->escapeFilterValue($filter->value),
                $filter instanceof Condition\GeoDistanceCondition => $filters[] = \sprintf(
                    '%s:(%s, %s, %s)',
                    $filter->field,
                    $filter->latitude,
                    $filter->longitude,
                    ($filter->distance / 1000) . ' km', // convert to km
                ),
                $filter instanceof Condition\GeoBoundingBoxCondition => $filters[] = \sprintf(
                    '%s:(%s, %s, %s, %s, %s, %s, %s, %s)',
                    $filter->field,
                    // TODO recheck if polygon is bigger as half of the earth if it not accidentally switches
                    $filter->northLatitude,
                    $filter->eastLongitude,
                    $filter->southLatitude,
                    $filter->eastLongitude,
                    $filter->southLatitude,
                    $filter->westLongitude,
                    $filter->northLatitude,
                    $filter->westLongitude,
                ),
                $filter instanceof Condition\AndCondition => $filters[] = '(' . $this->recursiveResolveFilterConditions($index, $filter->conditions, true, $query) . ')',
                $filter instanceof Condition\OrCondition => $filters[] = '(' . $this->recursiveResolveFilterConditions($index, $filter->conditions, false, $query) . ')',
                default => throw new \LogicException($filter::class . ' filter not implemented.'),
            };
        }

        if (\count($filters) < 2) {
            return \implode('', $filters);
        }

        return \implode($conjunctive ? ' && ' : ' || ', $filters);
    }
}
