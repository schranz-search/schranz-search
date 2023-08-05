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

namespace Schranz\Search\SEAL\Adapter\Loupe;

use Schranz\Search\SEAL\Adapter\IndexerInterface;
use Schranz\Search\SEAL\Marshaller\FlattenMarshaller;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Task\SyncTask;
use Schranz\Search\SEAL\Task\TaskInterface;

final class LoupeIndexer implements IndexerInterface
{
    private readonly FlattenMarshaller $marshaller;

    public function __construct(
        private readonly LoupeHelper $loupeHelper,
    ) {
        $this->marshaller = new FlattenMarshaller(
            dateAsInteger: true,
            separator: LoupeHelper::SEPERATOR,
            sourceField: LoupeHelper::SOURCE_FIELD,
        );
    }

    public function save(Index $index, array $document, array $options = []): ?TaskInterface
    {
        $loupe = $this->loupeHelper->getLoupe($index);

        $marshalledDocument = $this->marshaller->marshall($index->fields, $document);

        $loupe->addDocument($marshalledDocument);

        if (!($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask($document);
    }

    public function delete(Index $index, string $identifier, array $options = []): ?TaskInterface
    {
        $loupe = $this->loupeHelper->getLoupe($index);

        // TODO remove this when https://github.com/loupe-php/loupe/pull/10 is merged and released:
        $reflectionClass = new \ReflectionClass($loupe);
        $reflectionProperty = $reflectionClass->getProperty('engine');
        $reflectionProperty->setAccessible(true);

        /** @var \Loupe\Loupe\Internal\Engine $engine */
        $engine = $reflectionProperty->getValue($loupe);

        $connection = $engine->getConnection();

        $connection
            ->executeStatement(
                \sprintf(
                    'DELETE FROM %s WHERE user_id IN(:ids)',
                    \Loupe\Loupe\Internal\Index\IndexInfo::TABLE_NAME_DOCUMENTS,
                ),
                [
                    'ids' => \array_map(fn (string $id) => \Loupe\Loupe\Internal\LoupeTypes::convertToString($id), [$identifier]),
                ],
                [
                    'ids' => \Doctrine\DBAL\ArrayParameterType::STRING,
                ],
            );

        if (!($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
    }
}
