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

namespace Schranz\Search\SEAL\Adapter\Solr;

use Schranz\Search\SEAL\Adapter\IndexerInterface;
use Schranz\Search\SEAL\Marshaller\FlattenMarshaller;
use Schranz\Search\SEAL\Schema\Index;
use Schranz\Search\SEAL\Task\SyncTask;
use Schranz\Search\SEAL\Task\TaskInterface;
use Solarium\Client;

final class SolrIndexer implements IndexerInterface
{
    private readonly FlattenMarshaller $marshaller;

    public function __construct(
        private readonly Client $client,
    ) {
        $this->marshaller = new FlattenMarshaller(addRawFilterTextField: true);
    }

    public function save(Index $index, array $document, array $options = []): TaskInterface|null
    {
        $identifierField = $index->getIdentifierField();

        /** @var string|int|null $identifier */
        $identifier = $document[$identifierField->name] ?? null;

        $marshalledDocument = $this->marshaller->marshall($index->fields, $document);
        $marshalledDocument['id'] = (string) $identifier; // Solr currently does not support set another identifier then id: https://github.com/schranz-search/schranz-search/issues/87

        $update = $this->client->createUpdate();
        $indexDocument = $update->createDocument($marshalledDocument);

        $update->addDocuments([$indexDocument]);
        $update->addCommit();

        $this->client->getEndpoint()
            ->setCollection($index->name);

        $this->client->update($update);

        if (!($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask($document);
    }

    public function delete(Index $index, string $identifier, array $options = []): TaskInterface|null
    {
        $update = $this->client->createUpdate();
        $update->addDeleteById($identifier);
        $update->addCommit();

        $this->client->getEndpoint()
            ->setCollection($index->name);

        $this->client->update($update);

        if (!($options['return_slow_promise_result'] ?? false)) {
            return null;
        }

        return new SyncTask(null);
    }
}
