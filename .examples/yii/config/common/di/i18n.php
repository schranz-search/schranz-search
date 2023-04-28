<?php

declare(strict_types=1);

use Yiisoft\I18n\Locale;

/** @var $params array */

return [
    Locale::class => [
        'class' => Locale::class,
        '__construct()' => [
            $params['app']['locale'],
        ],
    ],
];
