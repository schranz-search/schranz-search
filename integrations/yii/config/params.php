<?php

declare(strict_types=1);

use Schranz\Search\Integration\Yii\Command\IndexCreateCommand;
use Schranz\Search\Integration\Yii\Command\IndexDropCommand;

return [
    'schranz-search/yii-module' => [
        'prefix' => '',
        'schemas' => [
            'app' => [
                'dir' => 'config/schemas',
            ],
        ],
        'engines' => [
            /* Example:
            'default' => [
                'adapter' => 'meilisearch://127.0.0.1:7700',
            ],
            */
        ],
    ],
    'yiisoft/yii-console' => [
        'commands' => [
            'schranz:search:index-create' => IndexCreateCommand::class,
            'schranz:search:index-drop' => IndexDropCommand::class,
        ],
    ],
];
