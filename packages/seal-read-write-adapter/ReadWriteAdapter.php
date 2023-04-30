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

namespace Schranz\Search\SEAL\Adapter\ReadWrite;

use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\Adapter\IndexerInterface;
use Schranz\Search\SEAL\Adapter\SchemaManagerInterface;
use Schranz\Search\SEAL\Adapter\SearcherInterface;

final class ReadWriteAdapter implements AdapterInterface
{
    public function __construct(
        private readonly AdapterInterface $readAdapter,
        private readonly AdapterInterface $writeAdapter,
    ) {
    }

    public function getSchemaManager(): SchemaManagerInterface
    {
        return $this->writeAdapter->getSchemaManager();
    }

    public function getIndexer(): IndexerInterface
    {
        return $this->writeAdapter->getIndexer();
    }

    public function getSearcher(): SearcherInterface
    {
        return $this->readAdapter->getSearcher();
    }
}
