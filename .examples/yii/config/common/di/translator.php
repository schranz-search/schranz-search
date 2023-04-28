<?php

declare(strict_types=1);

use Yiisoft\Aliases\Aliases;
use Yiisoft\Translator\CategorySource;
use Yiisoft\Translator\IntlMessageFormatter;
use Yiisoft\Translator\Message\Php\MessageSource;

/** @var array $params */

return [
    // Configure application CategorySource
    'translation.app' => [
        'definition' => static function (Aliases $aliases) use ($params) {
            return new CategorySource(
                $params['yiisoft/translator']['defaultCategory'],
                new MessageSource($aliases->get('@messages')),
                new IntlMessageFormatter(),
            );
        },
        'tags' => ['translation.categorySource'],
    ],
];
