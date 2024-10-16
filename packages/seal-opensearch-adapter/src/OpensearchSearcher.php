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

namespace Schranz\Search\SEAL\Adapter\Opensearch;

use OpenSearch\Client;
use OpenSearch\Common\Exceptions\Missing404Exception;
use Schranz\Search\SEAL\Adapter\SearcherInterface;
use Schranz\Search\SEAL\Marshaller\Marshaller;
use Schranz\Search\SEAL\Schema\Exception\FieldByPathNotFoundException;
use Schranz\Search\SEAL\Schema\Field;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Search\Condition;
use Schranz\Search\SEAL\Search\Result;
use Schranz\Search\SEAL\Search\Search;

final class OpensearchSearcher implements SearcherInterface
{
    private readonly Marshaller $marshaller;

    public function __construct(
        private readonly Client $client,
    ) {
        $this->marshaller = new Marshaller(
            geoPointFieldConfig: [
                'latitude' => 'lat',
                'longitude' => 'lon',
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
                $searchResult = $this->client->get([
                    'index' => $search->indexes[\array_key_first($search->indexes)]->name,
                    'id' => $search->filters[0]->identifier,
                ]);
            } catch (Missing404Exception) {
                return new Result(
                    $this->hitsToDocuments($search->indexes, []),
                    0,
                );
            }

            return new Result(
                $this->hitsToDocuments($search->indexes, [$searchResult]),
                1,
            );
        }

        $indexesNames = [];
        foreach ($search->indexes as $index) {
            $indexesNames[] = $index->name;
        }

        $query = $this->recursiveResolveFilterConditions($search->indexes, $search->filters, true);

        if ([] === $query) {
            $query['match_all'] = new \stdClass();
        }

        $sort = [];
        foreach ($search->sortBys as $field => $direction) {
            $sort[] = [$field => $direction];
        }

        $body = [
            'sort' => $sort,
            'query' => $query,
        ];

        if (0 !== $search->offset) {
            $body['from'] = $search->offset;
        }

        if ($search->limit) {
            $body['size'] = $search->limit;
        }

        $searchResult = $this->client->search([
            'index' => \implode(',', $indexesNames),
            'body' => $body,
        ]);

        return new Result(
            $this->hitsToDocuments($search->indexes, $searchResult['hits']['hits']),
            $searchResult['hits']['total']['value'],
        );
    }

    /**
     * @param Index[] $indexes
     * @param array<array<string, mixed>> $hits
     *
     * @return \Generator<int, array<string, mixed>>
     */
    private function hitsToDocuments(array $indexes, array $hits): \Generator
    {
        $indexesByInternalName = [];
        foreach ($indexes as $index) {
            $indexesByInternalName[$index->name] = $index;
        }

        /** @var array{_index: string, _source: array<string, mixed>} $hit */
        foreach ($hits as $hit) {
            $index = $indexesByInternalName[$hit['_index']] ?? null;
            if (!$index instanceof Index) {
                throw new \RuntimeException('SchemaMetadata for Index "' . $hit['_index'] . '" not found.');
            }

            yield $this->marshaller->unmarshall($index->fields, $hit['_source']);
        }
    }

    /**
     * @param Index[] $indexes
     */
    private function getFilterField(array $indexes, string $name): string
    {
        foreach ($indexes as $index) {
            try {
                $field = $index->getFieldByPath($name);

                if ($field instanceof Field\TextField) {
                    return $name . '.raw';
                }

                return $name;
            } catch (FieldByPathNotFoundException) {
                // ignore when field is not found and use go to next index instead
            }
        }

        return $name;
    }

    /**
     * @param Index[] $indexes
     * @param object[] $filters
     *
     * @return array<string|int, mixed>
     */
    private function recursiveResolveFilterConditions(array $indexes, array $filters, bool $conjunctive): array
    {
        $filterQueries = [];

        foreach ($filters as $filter) {
            match (true) {
                $filter instanceof Condition\IdentifierCondition => $filterQueries[]['ids']['values'][] = $filter->identifier,
                $filter instanceof Condition\SearchCondition => $filterQueries[]['bool']['must']['query_string']['query'] = $filter->query,
                $filter instanceof Condition\EqualCondition => $filterQueries[]['term'][$this->getFilterField($indexes, $filter->field)]['value'] = $filter->value,
                $filter instanceof Condition\NotEqualCondition => $filterQueries[]['bool']['must_not']['term'][$this->getFilterField($indexes, $filter->field)]['value'] = $filter->value,
                $filter instanceof Condition\GreaterThanCondition => $filterQueries[]['range'][$this->getFilterField($indexes, $filter->field)]['gt'] = $filter->value,
                $filter instanceof Condition\GreaterThanEqualCondition => $filterQueries[]['range'][$this->getFilterField($indexes, $filter->field)]['gte'] = $filter->value,
                $filter instanceof Condition\LessThanCondition => $filterQueries[]['range'][$this->getFilterField($indexes, $filter->field)]['lt'] = $filter->value,
                $filter instanceof Condition\LessThanEqualCondition => $filterQueries[]['range'][$this->getFilterField($indexes, $filter->field)]['lte'] = $filter->value,
                $filter instanceof Condition\InCondition => $filterQueries[]['terms'][$this->getFilterField($indexes, $filter->field)] = $filter->values,
                $filter instanceof Condition\NotInCondition => $filterQueries[]['bool']['must_not']['terms'][$this->getFilterField($indexes, $filter->field)] = $filter->values,
                $filter instanceof Condition\GeoDistanceCondition => $filterQueries[]['geo_distance'] = [
                    'distance' => $filter->distance,
                    $this->getFilterField($indexes, $filter->field) => [
                        'lat' => $filter->latitude,
                        'lon' => $filter->longitude,
                    ],
                ],
                $filter instanceof Condition\GeoBoundingBoxCondition => $filterQueries[]['geo_bounding_box'][$this->getFilterField($indexes, $filter->field)] = [
                    'top_left' => [
                        'lat' => $filter->northLatitude,
                        'lon' => $filter->westLongitude,
                    ],
                    'bottom_right' => [
                        'lat' => $filter->southLatitude,
                        'lon' => $filter->eastLongitude,
                    ],
                ],
                $filter instanceof Condition\AndCondition => $filterQueries[] = $this->recursiveResolveFilterConditions($indexes, $filter->conditions, true),
                $filter instanceof Condition\OrCondition => $filterQueries[] = $this->recursiveResolveFilterConditions($indexes, $filter->conditions, false),
                default => throw new \LogicException($filter::class . ' filter not implemented.'),
            };
        }

        if (\count($filterQueries) <= 1) {
            return $filterQueries[0] ?? [];
        }

        return [
            'bool' => [
                $conjunctive ? 'must' : 'should' => $filterQueries,
            ],
        ];
    }
}
