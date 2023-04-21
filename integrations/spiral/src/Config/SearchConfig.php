<?php

declare(strict_types=1);

namespace Schranz\Search\Integration\Spiral\Config;

use Spiral\Core\InjectableConfig;

/**
 * @experimental
 * @psalm-type TEngine = array{
 *     adapter: string,
 * }
 * @psalm-type TEngines = array<string, TEngine>
 * @psalm-type TSchema = array{
 *     dir: string,
 *     engine?: string,
 * }
 *
 * @psalm-type TSchemas = array<non-empty-string, TSchema>
 */
final class SearchConfig extends InjectableConfig
{
    public const CONFIG = 'schranz_search';

    /**
     * @var array{
     *     prefix: string,
     *     schemas: TSchemas,
     *     engines: TEngines>,
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
     * @return TSchemas
     */
    public function getSchemas(): array
    {
        return $this->config['schemas'];
    }

    /**
     * @return TEngines
     */
    public function getEngines(): array
    {
        return $this->config['engines'];
    }
}
