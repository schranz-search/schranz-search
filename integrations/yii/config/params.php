<?php

declare(strict_types=1);

/*
 * This file is part of the Schranz Search package.
 *
 * (c) Alexander Schranz <alexander@sulu.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Schranz\Search\Integration\Yii\Command\IndexCreateCommand;
use Schranz\Search\Integration\Yii\Command\IndexDropCommand;
use Schranz\Search\Integration\Yii\Command\ReindexCommand;

return [
    'schranz-search/yii-module' => [
        'index_name_prefix' => '',
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
        'reindex_providers' => [],
    ],
    'yiisoft/yii-console' => [
        'commands' => [
            'schranz:search:index-create' => IndexCreateCommand::class,
            'schranz:search:index-drop' => IndexDropCommand::class,
            'schranz:search:reindex' => ReindexCommand::class,
        ],
    ],
];
