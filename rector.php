<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\PHPUnit\Set\PHPUnitLevelSetList;
use Rector\PHPUnit\Set\PHPUnitSetList;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig, $directory): void {
    $rectorConfig->paths([
        $directory,
    ]);

    $rectorConfig->skip([
        $directory . '/vendor',
    ]);

    $rectorConfig->phpstanConfig($directory . '/phpstan.neon');

    // $rectorConfig->importNames();
    // $rectorConfig->importShortClasses(false);

    $rectorConfig->sets([
        SetList::CODE_QUALITY,
        LevelSetList::UP_TO_PHP_81,
    ]);

    $rectorConfig->sets([
        PHPUnitLevelSetList::UP_TO_PHPUNIT_90,
        PHPUnitSetList::PHPUNIT_91,
    ]);
};
