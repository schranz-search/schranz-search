<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $config = require __DIR__ . '/../../rector.php';
    $config($rectorConfig, __DIR__);

    $rectorConfig->paths([
        __DIR__ . '/app',
        __DIR__ . '/public',
        __DIR__ . '/tests',
    ]);
};
