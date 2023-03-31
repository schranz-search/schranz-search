<?php

declare(strict_types=1);

$phpCsConfig = require(dirname(__DIR__, 2) . '/.php-cs-fixer.dist.php');

$finder = (new PhpCsFixer\Finder())
    ->in([
        __DIR__ . '/app',
        __DIR__ . '/config',
        __DIR__ . '/database',
        __DIR__ . '/routes',
        __DIR__ . '/schemas',
        __DIR__ . '/tests',
    ])
    ->ignoreVCSIgnored(true);

$phpCsConfig->setFinder($finder);

return $phpCsConfig->setFinder($finder);
