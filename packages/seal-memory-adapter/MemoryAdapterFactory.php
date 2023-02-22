<?php

namespace Schranz\Search\SEAL\Adapter\Memory;

use Schranz\Search\SEAL\Adapter\AdapterFactoryInterface;
use Schranz\Search\SEAL\Adapter\AdapterInterface;

/**
 * @experimental
 */
class MemoryAdapterFactory implements AdapterFactoryInterface
{
    public function createAdapter(array $dsn): AdapterInterface
    {
        return new MemoryAdapter();
    }

    public static function getName(): string
    {
        return 'memory';
    }
}
