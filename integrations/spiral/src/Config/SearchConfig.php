<?php

declare(strict_types=1);

namespace Schranz\Search\Integration\Spiral\Config;

use Spiral\Core\InjectableConfig;

/**
 * @experimental
 */
final class SearchConfig extends InjectableConfig
{
    public const CONFIG = 'schranz_search';

    /**
     * @var array{
     *     prefix: string,
     *     schemas: array<string, array{
     *         dir: string,
     *         engine?: string,
     *     }>,
     *     engines: array<string, array{
     *         adapter: string,
     *     }>,
     * }
     */
    protected array $config = [
        'prefix' => '',
        'schemas' => [],
        'engines' => [],
    ];

    public function getPrefix(): string
    {
        return $this->config['prefix'];
    }

    /**
     * @return array<string, array{
     *     dir: string,
     *     engine?: string,
     * }>
     */
    public function getSchemas(): array
    {
        return $this->config['schemas'];
    }

    /**
     * @return array<string, array{
     *     adapter: string,
     * }>
     */
    public function getEngines(): array
    {
        return $this->config['engines'];
    }
}