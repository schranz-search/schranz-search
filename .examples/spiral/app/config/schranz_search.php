<?php

declare(strict_types=1);

return [
    'schemas' => [
        'app' => [
            'dir' => 'app/schemas',
        ],
    ],
    'engines' => [
        'algolia' => [
            'adapter' => 'algolia://%env(ALGOLIA_APPLICATION_ID)%:%env(ALGOLIA_ADMIN_API_KEY)%',
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
            'adapter' => 'opensearch://127.0.0.1:9200',
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
];
