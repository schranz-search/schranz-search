<?php

namespace Schranz\Search\SEAL;

use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\Schema\Schema;

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

    public function dropIndex(string $index): void
    {
        $this->adapter->getSchemaManager()->dropIndex($this->schema->indexes[$index]);
    }

    public function existIndex(string $index): void
    {
        $this->adapter->getSchemaManager()->existIndex($this->schema->indexes[$index]);
    }

    public function createIndex(string $index): void
    {
        $this->adapter->getSchemaManager()->createIndex($this->schema->indexes[$index]);
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
