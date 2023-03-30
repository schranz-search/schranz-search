<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Schema configs
    |--------------------------------------------------------------------------
    |
    | Define different directories for the schema loader.
    */

    'schemas' => [
        'app' => [
            'dir' => resource_path('schemas'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | engines
    |--------------------------------------------------------------------------
    |
    | Directory where the latte templates can be found.
    */

    'engines' => [
        'default' => [
            'adapter' => env(
                'ENGINE_URL',
                'meilisearch://127.0.0.1:7700',
            ),
        ],
    ],
];
