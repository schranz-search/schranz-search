<?php

namespace Schranz\Search\Integration\Spiral\Config;

use Spiral\Core\InjectableConfig;

final class SearchConfig extends InjectableConfig
{
    public const CONFIG = 'schranz_search';

    /**
     * @var array{
     *     schemas: array{
     *         string: array{
     *             dir: string,
     *             engine?: string,
     *         },
     *     },
     *     engines: array{
     *         string: array{
     *             adapter: string,
     *         },
     *     },
     * }
     */
    protected array $config = [
        'schemas' => [],
        'engines' => [],
    ];

    /**
     * @return array{
     *     string: array{
     *         dir: string,
     *         engine?: string,
     *     },
     * }
     */
    public function getSchemas(): array
    {
        return $this->config['schemas'];
    }

    /**
     * @return array{
     *     string: array{
     *         adapter: string,
     *     },
     * }
     */
    public function getEngines(): array
    {
        return $this->config['engines'];
    }
}
