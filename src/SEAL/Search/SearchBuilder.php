<?php

namespace Schranz\Search\SEAL\Search;

use Schranz\Search\SEAL\Adapter\ConnectionInterface;
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

    private ?int $limit = null;

    private ?int $offset = null;

    public function __construct(
        readonly private Schema $schema,
        readonly private ConnectionInterface $connection,
    ) {}

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

    public function offset(int $offset): static
    {
        $this->offset = $offset;

        return $this;
    }

    public function limit(int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    public function getResult(): Result
    {
        return $this->connection->search(new Search(
            $this->indexes,
            $this->filters,
            $this->limit,
            $this->offset,
        ));
    }
}
