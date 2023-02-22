<?php

namespace Schranz\Search\SEAL\Adapter;

/**
 * @experimental
 */
final class AdapterFactory
{
    private array $adapters;

    /**
     * @param iterable<string, AdapterFactoryInterface> $adapters
     */
    public function __construct(
        iterable $adapters
    ) {
        $this->adapters = [...$adapters];
    }

    public function createAdapter(string $dsn): AdapterInterface
    {
        $adapterName = \explode(':', $dsn, 2)[0];

        if (!$adapterName) {
            throw new \InvalidArgumentException(
                'Invalid DSN: "' . $dsn . '".'
            );
        }

        if (!isset($this->adapters[$adapterName])) {
            throw new \InvalidArgumentException(
                'Unknown Search adapter: "' . $adapterName . '" available adapters are "' . \implode('", "', \array_keys($this->adapters)) . '".'
            );
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

        return $this->adapters[$adapterName]->createAdapter($parsedDsn);
    }
}
