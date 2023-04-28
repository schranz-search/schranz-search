<?php

declare(strict_types=1);

use App\ViewInjection\CommonViewInjection;
use App\ViewInjection\LayoutViewInjection;
use App\ViewInjection\TranslatorViewInjection;
use Yiisoft\Definitions\Reference;
use Yiisoft\Yii\View\CsrfViewInjection;

return [
    'app' => [
        'charset' => 'UTF-8',
        'locale' => 'en',
        'name' => 'My Project',
    ],

    'yiisoft/aliases' => [
        'aliases' => require __DIR__ . '/aliases.php',
    ],

    'yiisoft/translator' => [
        'locale' => 'en',
        'fallbackLocale' => 'en',
        'defaultCategory' => 'app',
    ],

    'yiisoft/yii-view' => [
        'injections' => [
            Reference::to(CommonViewInjection::class),
            Reference::to(CsrfViewInjection::class),
            Reference::to(LayoutViewInjection::class),
            Reference::to(TranslatorViewInjection::class),
        ],
    ],

    'schranz-search/yii-module' => [
        'schemas' => [
            'algolia' => [
                'dir' => 'config/schemas',
                'engine' => 'algolia',
            ],
            'elasticsearch' => [
                'dir' => 'config/schemas',
                'engine' => 'elasticsearch',
            ],
            'meilisearch' => [
                'dir' => 'config/schemas',
                'engine' => 'meilisearch',
            ],
            'memory' => [
                'dir' => 'config/schemas',
                'engine' => 'memory',
            ],
            'opensearch' => [
                'dir' => 'config/schemas',
                'engine' => 'opensearch',
            ],
            'redisearch' => [
                'dir' => 'config/schemas',
                'engine' => 'redisearch',
            ],
            'solr' => [
                'dir' => 'config/schemas',
                'engine' => 'solr',
            ],
            'typesense' => [
                'dir' => 'config/schemas',
                'engine' => 'typesense',
            ],
        ],
        'engines' => [
            'algolia' => [
                'adapter' => 'algolia://' . (\getenv('ALGOLIA_APPLICATION_ID') ?: $_ENV['ALGOLIA_APPLICATION_ID']) . ':' . (\getenv('ALGOLIA_ADMIN_API_KEY') ?: $_ENV['ALGOLIA_ADMIN_API_KEY']),
            ],
            'elasticsearch' => [
                'adapter' => 'elasticsearch://127.0.0.1:9200',
            ],
            'meilisearch' => [
                'adapter' => 'meilisearch://127.0.0.1:7700',
            ],
            'memory' => [
                'adapter' => 'memory://',
            ],
            'opensearch' => [
                'adapter' => 'opensearch://127.0.0.1:9201',
            ],
            'redisearch' => [
                'adapter' => 'redis://supersecure@127.0.0.1:6379',
            ],
            'solr' => [
                'adapter' => 'solr://127.0.0.1:8983',
            ],
            'typesense' => [
                'adapter' => 'typesense://S3CR3T@127.0.0.1:8108',
            ],

            // ...
            'multi' => [
                'adapter' => 'multi://elasticsearch?adapters[]=opensearch',
            ],
            'read-write' => [
                'adapter' => 'read-write://elasticsearch?write=multi',
            ],
        ],
    ],
];
