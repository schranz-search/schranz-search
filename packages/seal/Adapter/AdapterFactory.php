<?php

declare(strict_types=1);

namespace Schranz\Search\SEAL\Adapter;

/**
 * @experimental
 */
final class AdapterFactory
{
    /**
     * @var array<string, AdapterFactoryInterface>
     */
    private array $factories;

    /**
     * @param iterable<string, AdapterFactoryInterface> $factories
     */
    public function __construct(
        iterable $factories,
    ) {
        $this->factories = [...$factories];
    }

    public function createAdapter(string $dsn): AdapterInterface
    {
        /** @var string|null $adapterName */
        $adapterName = \explode(':', $dsn, 2)[0];

        if (!$adapterName) {
            throw new \InvalidArgumentException(
                'Invalid DSN: "' . $dsn . '".',
            );
        }

        if (!isset($this->factories[$adapterName])) {
            throw new \InvalidArgumentException(
                'Unknown Search adapter: "' . $adapterName . '" available adapters are "' . \implode('", "', \array_keys($this->factories)) . '".',
            );
        }

        /**
         * @var array{
         *     scheme: string,
         *     host: string,
         *     port?: int,
         *     user?: string,
         *     pass?: string,
         *     path?: string,
         *     query?: string,
         *     fragment?: string,
         * }|false $parsedDsn
         */
        $parsedDsn = \parse_url($dsn);

        // make DSN like algolia://YourApplicationID:YourAdminAPIKey parseable
        if (false === $parsedDsn) {
            /**
             * @var array{
             *     scheme: string,
             *     host: string,
             *     port?: int,
             *     user?: string,
             *     pass?: string,
             *     path?: string,
             *     query?: string,
             *     fragment?: string,
             * } $parsedDsn
             */
            $parsedDsn = \parse_url($dsn . '@' . $adapterName);
        }

        /** @var array<string, string> $query */
        $query = [];
        if (isset($parsedDsn['query'])) {
            \parse_str($parsedDsn['query'], $query);
        }

        $parsedDsn['query'] = $query;

        /**
         * @var array{
         *     scheme: string,
         *     host: string,
         *     port?: int,
         *     user?: string,
         *     pass?: string,
         *     path?: string,
         *     query: array<string, string>,
         *     fragment?: string,
         * } $parsedDsn
         */

        return $this->factories[$adapterName]->createAdapter($parsedDsn);
    }
}
