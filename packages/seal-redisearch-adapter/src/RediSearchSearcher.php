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

namespace Schranz\Search\SEAL\Adapter\RediSearch;

use Schranz\Search\SEAL\Adapter\SearcherInterface;
use Schranz\Search\SEAL\Marshaller\Marshaller;
use Schranz\Search\SEAL\Schema\Exception\FieldByPathNotFoundException;
use Schranz\Search\SEAL\Schema\Field;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Search\Condition;
use Schranz\Search\SEAL\Search\Result;
use Schranz\Search\SEAL\Search\Search;

final class RediSearchSearcher implements SearcherInterface
{
    private readonly Marshaller $marshaller;

    public function __construct(
        private readonly \Redis $client,
    ) {
        $this->marshaller = new Marshaller(
            addRawFilterTextField: true,
            geoPointFieldConfig: [
                'latitude' => 1,
                'longitude' => 0,
                'separator' => ',',
                'multiple' => true,
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
            /** @var string|false $jsonGet */
            $jsonGet = $this->client->rawCommand(
                'JSON.GET',
                $search->indexes[\array_key_first($search->indexes)]->name . ':' . $search->filters[0]->identifier,
            );

            if (false === $jsonGet) {
                return new Result(
                    $this->hitsToDocuments($search->indexes, []),
                    0,
                );
            }

            /** @var array<string, mixed> $document */
            $document = \json_decode($jsonGet, true, flags: \JSON_THROW_ON_ERROR);

            return new Result(
                $this->hitsToDocuments($search->indexes, [$document]),
                1,
            );
        }

        if (1 !== \count($search->indexes)) {
            throw new \RuntimeException('RediSearch does not yet support search multiple indexes: https://github.com/schranz-search/schranz-search/issues/93');
        }

        $index = $search->indexes[\array_key_first($search->indexes)];
        $parameters = [];

        $query = $this->recursiveResolveFilterConditions($index, $search->filters, $search->indexes, true, $parameters) ?: '*';

        $arguments = [];
        foreach ($search->sortBys as $field => $direction) {
            $arguments[] = 'SORTBY';
            $arguments[] = $this->escapeFilterValue($field);
            $arguments[] = \strtoupper((string) $this->escapeFilterValue($direction));
        }

        if ($search->offset || $search->limit) {
            $arguments[] = 'LIMIT';
            $arguments[] = $search->offset;
            $arguments[] = ($search->limit ?: 10);
        }

        if ([] !== $parameters) {
            $arguments[] = 'PARAMS';
            $arguments[] = \count($parameters) * 2;
            foreach ($parameters as $key => $value) {
                $arguments[] = $key;
                $arguments[] = $value;
            }
        }

        $arguments[] = 'DIALECT';
        $arguments[] = '3';

        /** @var mixed[]|false $result */
        $result = $this->client->rawCommand(
            'FT.SEARCH',
            $index->name,
            $query,
            ...$arguments,
        );

        if (false === $result) {
            throw $this->createRedisLastErrorException();
        }

        /** @var int $total */
        $total = $result[0];

        $documents = [];
        foreach ($result as $item) {
            if (!\is_array($item)) {
                continue;
            }

            $previousValue = null;
            foreach ($item as $value) {
                if ('$' === $previousValue) {
                    /** @var array<string, mixed> $document */
                    $document = \json_decode($value, true, flags: \JSON_THROW_ON_ERROR)[0]; // @phpstan-ignore-line

                    $documents[] = $document;
                }

                $previousValue = $value;
            }
        }

        return new Result(
            $this->hitsToDocuments($search->indexes, $documents),
            $total,
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

        foreach ($hits as $hit) {
            yield $this->marshaller->unmarshall($index->fields, $hit);
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
                    $name .= '__raw';
                }

                break;
            } catch (FieldByPathNotFoundException) {
                // ignore when field is not found and use go to next index instead
            }
        }

        return \str_replace('.', '__', $name);
    }

    private function createRedisLastErrorException(): \RuntimeException
    {
        $lastError = $this->client->getLastError();
        $this->client->clearLastError();

        return new \RuntimeException('Redis: ' . $lastError);
    }

    private function escapeFilterValue(string|int|float|bool $value): string
    {
        return match (true) {
            \is_string($value) => \str_replace(
                ["\n", "\r", "\t"],
                ["\\\n", "\\\r", "\\\t"], // double escaping required see https://github.com/RediSearch/RediSearch/issues/4092#issuecomment-1819932938
                \addcslashes($value, ',./(){}[]:;~!@#$%^&*-=+|\'`"<>? '),
            ),
            \is_bool($value) => $value ? 'true' : 'false',
            default => (string) $value,
        };
    }

    /**
     * @param object[] $conditions
     * @param Index[] $indexes
     * @param array<string, string> $parameters
     */
    private function recursiveResolveFilterConditions(Index $index, array $conditions, array $indexes, bool $conjunctive, array &$parameters): string
    {
        $filters = [];

        foreach ($conditions as $filter) {
            match (true) {
                $filter instanceof Condition\SearchCondition => $filters[] = '%%' . \implode('%% ', \explode(' ', $this->escapeFilterValue($filter->query))) . '%%', // levenshtein of 2 per word
                $filter instanceof Condition\IdentifierCondition => $filters[] = '@' . $index->getIdentifierField()->name . ':{' . $this->escapeFilterValue($filter->identifier) . '}',
                $filter instanceof Condition\EqualCondition => $filters[] = '@' . $this->getFilterField($indexes, $filter->field) . ':{' . $this->escapeFilterValue($filter->value) . '}',
                $filter instanceof Condition\NotEqualCondition => $filters[] = '-@' . $this->getFilterField($indexes, $filter->field) . ':{' . $this->escapeFilterValue($filter->value) . '}',
                $filter instanceof Condition\GreaterThanCondition => $filters[] = '@' . $this->getFilterField($indexes, $filter->field) . ':[(' . $this->escapeFilterValue($filter->value) . ' inf]',
                $filter instanceof Condition\GreaterThanEqualCondition => $filters[] = '@' . $this->getFilterField($indexes, $filter->field) . ':[' . $this->escapeFilterValue($filter->value) . ' inf]',
                $filter instanceof Condition\LessThanCondition => $filters[] = '@' . $this->getFilterField($indexes, $filter->field) . ':[-inf (' . $this->escapeFilterValue($filter->value) . ']',
                $filter instanceof Condition\LessThanEqualCondition => $filters[] = '@' . $this->getFilterField($indexes, $filter->field) . ':[-inf ' . $this->escapeFilterValue($filter->value) . ']',
                $filter instanceof Condition\GeoDistanceCondition => $filters[] = \sprintf(
                    '@%s:[%s %s %s]',
                    $this->getFilterField($indexes, $filter->field),
                    $filter->longitude,
                    $filter->latitude,
                    ($filter->distance / 1000) . ' km',
                ),
                $filter instanceof Condition\GeoBoundingBoxCondition => throw new \RuntimeException('Not supported by RediSearch: https://github.com/RediSearch/RediSearch/issues/680 or https://github.com/RediSearch/RediSearch/issues/5032'),
                /* Keep here for future implementation:
                $filter instanceof Condition\GeoBoundingBoxCondition => ($filters[] = \sprintf(
                    '@%s:[WITHIN $filter_%s]',
                    $this->getFilterField($search->indexes, $filter->field),
                    $key,
                )) && ($parameters['filter_' . $key] = \sprintf(
                    'POLYGON((%s %s, %s %s, %s %s, %s %s, %s %s))',
                    $filter->westLongitude,
                    $filter->northLatitude,
                    $filter->westLongitude,
                    $filter->southLatitude,
                    $filter->eastLongitude,
                    $filter->southLatitude,
                    $filter->eastLongitude,
                    $filter->northLatitude,
                    $filter->westLongitude,
                    $filter->northLatitude,
                )),
                */
                $filter instanceof Condition\AndCondition => $filters[] = '(' . $this->recursiveResolveFilterConditions($index, $filter->conditions, $indexes, true, $parameters) . ')',
                $filter instanceof Condition\OrCondition => $filters[] = '(' . $this->recursiveResolveFilterConditions($index, $filter->conditions, $indexes, false, $parameters) . ')',
                default => throw new \LogicException($filter::class . ' filter not implemented.'),
            };
        }

        if (\count($filters) < 2) {
            return \implode('', $filters);
        }

        return \implode($conjunctive ? ' ' : ' | ', $filters);
    }
}
