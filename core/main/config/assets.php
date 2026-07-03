<?php

return [
    'offline' => env('ASSETS_OFFLINE', true),
    'enable_version' => env('ASSETS_ENABLE_VERSION', true),
    'version' => env('ASSETS_VERSION', get_app_version()),
    'scripts' => [
        'framework7-js',
    ],
    'styles' => [
        'framework7-css',
    ],
    'resources' => [
        'scripts' => [
            'framework7-js' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => '/core/main/js/framework7-bundle.js',
                ],
            ],

        ],
        'styles' => [
            'framework7-css' => [
                'use_cdn' => false,
                'location' => 'header',
                'src' => [
                    'local' => '/core/main/css/framework7-bundle.css',
                ],
            ],
        ],
    ],
];
