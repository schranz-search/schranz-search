<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL;

use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\Exception\DocumentNotFoundException;
use Schranz\Search\SEAL\Schema\Schema;
use Schranz\Search\SEAL\Search\Condition\IdentifierCondition;
use Schranz\Search\SEAL\Search\SearchBuilder;
use Schranz\Search\SEAL\Task\MultiTask;
use Schranz\Search\SEAL\Task\TaskInterface;

final class Engine
{
    public function __construct(
        readonly private AdapterInterface $adapter,
        readonly private Schema $schema,
    ) {
    }

    /**
     * @param array<string, mixed> $document
     * @param array{return_slow_promise_result?: true} $options
     *
     * @return ($options is non-empty-array ? TaskInterface<array<string, mixed>> : null)
     */
    public function saveDocument(string $index, array $document, array $options = []): ?TaskInterface
    {
        return $this->adapter->getIndexer()->save(
            $this->schema->indexes[$index],
            $document,
            $options,
        );
    }

    /**
     * @param array{return_slow_promise_result?: true} $options
     *
     * @return ($options is non-empty-array ? TaskInterface<null|void> : null)
     */
    public function deleteDocument(string $index, string $identifier, array $options = []): ?TaskInterface
    {
        return $this->adapter->getIndexer()->delete(
            $this->schema->indexes[$index],
            $identifier,
            $options,
        );
    }

    /**
     * @throws DocumentNotFoundException
     *
     * @return array<string, mixed>
     */
    public function getDocument(string $index, string $identifier): array
    {
        $documents = [...$this->createSearchBuilder()
            ->addIndex($index)
            ->addFilter(new IdentifierCondition($identifier))
            ->limit(1)
            ->getResult()];

        /** @var array<string, mixed>|null $document */
        $document = $documents[0] ?? null;

        if (null === $document) {
            throw new DocumentNotFoundException(\sprintf(
                'Document with the identifier "%s" not found in index "%s".',
                $identifier,
                $index,
            ));
        }

        return $document;
    }

    public function createSearchBuilder(): SearchBuilder
    {
        return new SearchBuilder(
            $this->schema,
            $this->adapter->getSearcher(),
        );
    }

    /**
     * @param array{return_slow_promise_result?: true} $options
     *
     * @return ($options is non-empty-array ? TaskInterface<null|void> : null)
     */
    public function createIndex(string $index, array $options = []): ?TaskInterface
    {
        return $this->adapter->getSchemaManager()->createIndex($this->schema->indexes[$index], $options);
    }

    /**
     * @param array{return_slow_promise_result?: true} $options
     *
     * @return ($options is non-empty-array ? TaskInterface<null|void> : null)
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
     * @param array{return_slow_promise_result?: bool} $options
     *
     * @return ($options is non-empty-array ? TaskInterface<null> : null)
     */
    public function createSchema(array $options = []): ?TaskInterface
    {
        $tasks = [];
        foreach ($this->schema->indexes as $index) {
            $tasks[] = $this->adapter->getSchemaManager()->createIndex($index, $options);
        }

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new MultiTask($tasks); // @phpstan-ignore-line
    }

    /**
     * @param array{return_slow_promise_result?: bool} $options
     *
     * @return ($options is non-empty-array ? TaskInterface<null> : null)
     */
    public function dropSchema(array $options = []): ?TaskInterface
    {
        $tasks = [];
        foreach ($this->schema->indexes as $index) {
            $tasks[] = $this->adapter->getSchemaManager()->dropIndex($index, $options);
        }

        if (true !== ($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new MultiTask($tasks); // @phpstan-ignore-line
    }
}
