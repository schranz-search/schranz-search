<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter\OpenAIPostgreSQL;

use OpenAI\Client;
use Schranz\Search\SEAL\Adapter\SearcherInterface;
use Schranz\Search\SEAL\Marshaller\FlattenMarshaller;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Search\Condition;
use Schranz\Search\SEAL\Search\Result;
use Schranz\Search\SEAL\Search\Search;

final class OpenAIPostgreSQLSearcher implements SearcherInterface
{
    private readonly FlattenMarshaller $marshaller;

    public function __construct(
        private readonly Client $openAiClient,
        private readonly \PDO $pdoClient,
    ) {
        $this->marshaller = new FlattenMarshaller();
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
            $indexName = $search->indexes[\array_key_first($search->indexes)]->name;
            $identifier = $search->filters[0]->identifier;

            $statement = $this->pdoClient->prepare('SELECT document FROM ' . $indexName . ' WHERE identifier = :identifier');
            $statement->execute([
                'identifier' => $identifier,
            ]);

            $documentJson = $statement->fetchColumn();

            if (!$documentJson) {
                return new Result(
                    $this->hitsToDocuments($search->indexes, []),
                    0,
                );
            }

            /** @var array<string, mixed> $document */
            $document = \json_decode($documentJson, true, 512, \JSON_THROW_ON_ERROR);

            return new Result(
                $this->hitsToDocuments($search->indexes, [$document]),
                1,
            );
        }

        if (1 !== \count($search->indexes)) {
            throw new \RuntimeException('Meilisearch does not yet support search multiple indexes: https://github.com/schranz-search/schranz-search/issues/28');
        }

        $index = $search->indexes[\array_key_first($search->indexes)];

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

        if ($query) {
            // TODO OpenAI recommends replacing newlines with spaces for best results: https://supabase.com/blog/openai-embeddings-postgres-vector
            /*
            $response = $this->openAiClient->embeddings()->create([
                'model' => 'text-embedding-ada-002',
                'input' => $query,
            ]);

            $vectors = $response->embeddings[0]->embedding;
            */
        }

        $searchParams = [];
        if ([] !== $filters) {
            $searchParams = ['filter' => \implode(' AND ', $filters)];
        }

        if (0 !== $search->offset) {
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
     * @return \Generator<int, array<string, mixed>>
     */
    private function hitsToDocuments(array $indexes, iterable $hits): \Generator
    {
        $index = $indexes[\array_key_first($indexes)];

        foreach ($hits as $hit) {
            yield $this->marshaller->unmarshall($index->fields, $hit);
        }
    }
}
