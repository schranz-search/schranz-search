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

namespace Schranz\Search\SEAL;

use Schranz\Search\SEAL\Exception\DocumentNotFoundException;
use Schranz\Search\SEAL\Search\SearchBuilder;
use Schranz\Search\SEAL\Task\TaskInterface;

interface EngineInterface
{
    /**
     * @param array<string, mixed> $document
     * @param array{return_slow_promise_result?: true} $options
     *
     * @return ($options is non-empty-array ? TaskInterface<array<string, mixed>> : null)
     */
    public function saveDocument(string $index, array $document, array $options = []): ?TaskInterface;

    /**
     * @param array{return_slow_promise_result?: true} $options
     *
     * @return ($options is non-empty-array ? TaskInterface<null|void> : null)
     */
    public function deleteDocument(string $index, string $identifier, array $options = []): ?TaskInterface;

    /**
     * @throws DocumentNotFoundException
     *
     * @return array<string, mixed>
     */
    public function getDocument(string $index, string $identifier): array;

    public function createSearchBuilder(): SearchBuilder;

    /**
     * @param array{return_slow_promise_result?: true} $options
     *
     * @return ($options is non-empty-array ? TaskInterface<null|void> : null)
     */
    public function createIndex(string $index, array $options = []): ?TaskInterface;

    /**
     * @param array{return_slow_promise_result?: true} $options
     *
     * @return ($options is non-empty-array ? TaskInterface<null|void> : null)
     */
    public function dropIndex(string $index, array $options = []): ?TaskInterface;

    public function existIndex(string $index): bool;

    /**
     * @param array{return_slow_promise_result?: bool} $options
     *
     * @return ($options is non-empty-array ? TaskInterface<null> : null)
     */
    public function createSchema(array $options = []): ?TaskInterface;

    /**
     * @param array{return_slow_promise_result?: bool} $options
     *
     * @return ($options is non-empty-array ? TaskInterface<null> : null)
     */
    public function dropSchema(array $options = []): ?TaskInterface;
}
