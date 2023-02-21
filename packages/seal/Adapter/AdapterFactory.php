<?php

namespace Schranz\Search\SEAL\Adapter;

/**
 * @experimental
 */
final class AdapterFactory
{
    /**
     * @param array<string, AdapterFactoryInterface> $adapters
     */
    public function __construct(
        private array $adapters
    ) {}

    public function getAdapter(string $dsn): AdapterInterface
    {
        $adapterName = \explode(':', $dsn, 2)[0];

        if (!$adapterName) {
            throw new \InvalidArgumentException('Invalid DSN: ' . $dsn);
        }

        if (!isset($this->adapters[$adapterName])) {
            throw new \InvalidArgumentException('Unknown adapter: ' . $adapterName);
        }

        $parsedDsn = parse_url($dsn);

        // make DSN like algolia://YourApplicationID:YourAdminAPIKey parseable
        if ($parsedDsn === false) {
            $parsedDsn = parse_url($dsn . '@' . $adapterName);
        }

        $query = [];
        if (isset($parsedDsn['query'])) {
            parse_str($parsedDsn['query'], $query);
        }

        $parsedDsn['query'] = $query;

        return $this->adapters[$adapterName]->getAdapter($parsedDsn);
    }
}
