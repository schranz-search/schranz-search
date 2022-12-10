<?php

namespace Schranz\Search\SEAL;

use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\Exception\DocumentNotFoundException;
use Schranz\Search\SEAL\Schema\Schema;
use Schranz\Search\SEAL\Search\Filter\IdentifierFilter;
use Schranz\Search\SEAL\Search\SearchBuilder;

final class Engine
{
    public function __construct(
        readonly private AdapterInterface $adapter,
        readonly private Schema $schema,
    ) {}

    /**
     * @param array<string, mixed> $document
     *
     * @return array<string, mixed>
     */
    public function indexDocument(string $index, array $document): array
    {
        return $this->adapter->getConnection()->index(
            $this->schema->indexes[$index],
            $document
        );
    }

    public function deleteDocument(string $index, string $identifier): void
    {
        $this->adapter->getConnection()->delete(
            $this->schema->indexes[$index],
            $identifier
        );
    }

    /**
     * @return array<string, mixed>
     *
     * @throws DocumentNotFoundException
     */
    public function findDocument(string $index, string $identifier): array
    {
        $documents = [...$this->createSearchBuilder()
            ->addIndex($index)
            ->addFilter(new IdentifierFilter($identifier))
            ->limit(1)
            ->getResult()];

        $document = $documents[0] ?? null;

        if ($document === null) {
            throw new DocumentNotFoundException(\sprintf(
                'Document with the identifier "%s" not found in index "%s.',
                $identifier,
                $index
            ));
        }

        return $document;
    }

    public function createSearchBuilder(): SearchBuilder
    {
        return new SearchBuilder(
            $this->schema,
            $this->adapter->getConnection()
        );
    }

    public function createIndex(string $index): void
    {
        $this->adapter->getSchemaManager()->createIndex($this->schema->indexes[$index]);
    }

    public function dropIndex(string $index): void
    {
        $this->adapter->getSchemaManager()->dropIndex($this->schema->indexes[$index]);
    }

    public function existIndex(string $index): void
    {
        $this->adapter->getSchemaManager()->existIndex($this->schema->indexes[$index]);
    }

    public function createSchema(): void
    {
        foreach ($this->schema->indexes as $index) {
            $this->adapter->getSchemaManager()->createIndex($index);
        }
    }

    public function dropSchema(): void
    {
        foreach ($this->schema->indexes as $index) {
            $this->adapter->getSchemaManager()->dropIndex($index);
        }
    }
}
