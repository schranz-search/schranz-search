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

namespace Schranz\Search\SEAL\Reindex;

interface ReindexProviderInterface
{
    /**
     * Returns how many documents this provider will provide. Returns `null` if the total is unknown.
     */
    public function total(): ?int;

    /**
     * The reindex provider returns a Generator which provides the documents to reindex.
     *
     * @return \Generator<array<string, mixed>>
     */
    public function provide(): \Generator;

    /**
     * The name of the index for which the documents are for.
     */
    public static function getIndex(): string;
}
