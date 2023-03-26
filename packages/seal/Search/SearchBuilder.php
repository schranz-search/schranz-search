<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Search;

use Schranz\Search\SEAL\Adapter\SearcherInterface;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Schema\Schema;

final class SearchBuilder
{
    /**
     * @var array<string, Index>
     */
    private array $indexes = [];

    /**
     * @var object[]
     */
    private array $filters = [];

    /**
     * @var array<string, 'asc'|'desc'>
     */
    private array $sortBys = [];

    private int $offset = 0;

    private ?int $limit = null;

    public function __construct(
        readonly private Schema $schema,
        readonly private SearcherInterface $searcher,
    ) {
    }

    public function addIndex(string $name): static
    {
        $this->indexes[$name] = $this->schema->indexes[$name];

        return $this;
    }

    public function addFilter(object $filter): static
    {
        $this->filters[] = $filter;

        return $this;
    }

    /**
     * @param 'asc'|'desc' $direction
     */
    public function addSortBy(string $field, string $direction): static
    {
        $this->sortBys[$field] = $direction;

        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    public function offset(int $offset): static
    {
        $this->offset = $offset;

        return $this;
    }

    public function getResult(): Result
    {
        return $this->searcher->search(new Search(
            $this->indexes,
            $this->filters,
            $this->sortBys,
            $this->limit,
            $this->offset,
        ));
    }
}
