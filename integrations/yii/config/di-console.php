<?php

declare(strict_types=1);

use Psr\Container\ContainerInterface;
use Schranz\Search\Integration\Yii\Command\IndexCreateCommand;
use Schranz\Search\Integration\Yii\Command\IndexDropCommand;
use Schranz\Search\SEAL\EngineRegistry;

$diConfig[IndexCreateCommand::class] = static function (ContainerInterface $container) {
    /** @var EngineRegistry $engineRegistry */
    $engineRegistry = $container->get(EngineRegistry::class);

    return new IndexCreateCommand($engineRegistry);
};

$diConfig[IndexDropCommand::class] = static function (ContainerInterface $container) {
    /** @var EngineRegistry $engineRegistry */
    $engineRegistry = $container->get(EngineRegistry::class);

    return new IndexDropCommand($engineRegistry);
};

return $diConfig;
