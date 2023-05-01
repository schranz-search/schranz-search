<?php

declare(strict_types=1);

namespace App\Helper;

use Schranz\Search\SEAL\Adapter\AdapterInterface;
use Schranz\Search\SEAL\EngineInterface;

class AdapterClassHelper
{
    public static function getAdapterClass(EngineInterface $engine): string
    {
        $reflection = new \ReflectionClass($engine);
        $propertyReflection = $reflection->getProperty('adapter');
        $propertyReflection->setAccessible(true);

        /** @var AdapterInterface $object */
        $object = $propertyReflection->getValue($engine);

        return $object::class;
    }
}
