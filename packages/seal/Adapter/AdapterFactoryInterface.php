<?php

namespace Schranz\Search\SEAL\Adapter;

/**
 * @experimental
 */
interface AdapterFactoryInterface
{
    /**
     * @param array{
     *     scheme: string,
     *     host: string,
     *     port?: int,
     *     user?: string,
     *     pass?: string,
     *     path?: string,
     *     query: array<string, string>,
     *     fragment?: string,
     * } $dsn
     */
    public function getAdapter(array $dsn): AdapterInterface;

    /**
     * Returns the expected DSN scheme for this adapter.
     */
    public static function getName(): string;
}
