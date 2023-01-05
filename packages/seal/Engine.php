<?php

namespace Schranz\Search\SEAL;

use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\Exception\DocumentNotFoundException;
use Schranz\Search\SEAL\Schema\Schema;
use Schranz\Search\SEAL\Search\Condition\IdentifierCondition;
use Schranz\Search\SEAL\Search\SearchBuilder;
use Schranz\Search\SEAL\Task\TaskInterface;

final class Engine
{
    public function __construct(
        readonly private AdapterInterface $adapter,
        readonly private Schema $schema,
    ) {}

    /**
     * @template T of bool
     *
     * @param array{return_slow_promise_result: T} $options
     *
     * @return (T is true ? TaskInterface<array<string, mixed>> : null)
     */
    public function saveDocument(string $index, array $document, array $options = []): ?TaskInterface
    {
        return $this->adapter->getConnection()->save(
            $this->schema->indexes[$index],
            $document,
            $options,
        );
    }

    /**
     * @template T of bool
     *
     * @param array{return_slow_promise_result: T} $options
     *
     * @return (T is true ? TaskInterface<null> : null)
     */
    public function deleteDocument(string $index, string $identifier, array $options = []): ?TaskInterface
    {
        return $this->adapter->getConnection()->delete(
            $this->schema->indexes[$index],
            $identifier,
            $options,
        );
    }

    /**
     * @return array<string, mixed>
     *
     * @throws DocumentNotFoundException
     */
    public function getDocument(string $index, string $identifier): array
    {
        $documents = [...$this->createSearchBuilder()
            ->addIndex($index)
            ->addFilter(new IdentifierCondition($identifier))
            ->limit(1)
            ->getResult()];

        $document = $documents[0] ?? null;

        if ($document === null) {
            throw new DocumentNotFoundException(\sprintf(
                'Document with the identifier "%s" not found in index "%s".',
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

    /**
     * @template T of bool
     *
     * @param array{return_slow_promise_result: T} $options
     *
     * @return (T is true ? TaskInterface<null> : null)
     */
    public function createIndex(string $index, array $options = []): ?TaskInterface
    {
        return $this->adapter->getSchemaManager()->createIndex($this->schema->indexes[$index], $options);
    }

    /**
     * @template T of bool
     *
     * @param array{return_slow_promise_result: T} $options
     *
     * @return (T is true ? TaskInterface<null> : null)
     */
    public function dropIndex(string $index, array $options = []): ?TaskInterface
    {
        return $this->adapter->getSchemaManager()->dropIndex($this->schema->indexes[$index], $options);
    }

    public function existIndex(string $index): bool
    {
        return $this->adapter->getSchemaManager()->existIndex($this->schema->indexes[$index]);
    }

    /**
     * @template T of bool
     *
     * @param array{return_slow_promise_result: T} $options
     *
     * @return (T is true ? TaskInterface<null> : null)
     */
    public function createSchema(array $options = []): ?TaskInterface
    {
        $tasks = [];
        foreach ($this->schema->indexes as $index) {
            $tasks[] = $this->adapter->getSchemaManager()->createIndex($index, $options);
        }

        return null;

        // TODO return TaskInterface for $returnWaitPromise = true
    }

    /**
     * @template T of bool
     *
     * @param array{return_slow_promise_result: T} $options
     *
     * @return (T is true ? TaskInterface<null> : null)
     */
    public function dropSchema(array $options = []): ?TaskInterface
    {
        $tasks = [];
        foreach ($this->schema->indexes as $index) {
            $tasks[] = $this->adapter->getSchemaManager()->dropIndex($index, $options);
        }

        return null;

        // TODO return TaskInterface for $returnWaitPromise = true
    }
}
