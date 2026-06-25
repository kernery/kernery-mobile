<?php

return [
    'offline' => env('ASSETS_OFFLINE', true), // serve assets offline
    'enable_version' => env('ASSETS_ENABLE_VERSION', false), // version your assets
    'version' => env('ASSETS_VERSION', time()), // use php timestamp to version assets
    /*
    * Pass the name of the asset into this array
    * e.g 'bootstrap-4-script' the name is used as an argument for the Asset facade
    * Like this Asset::script('bootstrap-4-script')
    * see the resources below.
    */
    'scripts' => [
        // 'app-js'
    ],
    'styles' => [
        // 'bootstrap-css'
    ],
    'resources' => [
        'scripts' => [
            'app-js' => [
                'use_cdn' => false,
                'location' => 'footer',
                'src' => [
                    'local' => '/js/app.js',
                ],
            ],
        ],
        'styles' => [
            'bootstrap-css' => [
                'use_cdn' => true,
                'location' => 'header',
                'src' => [
                    'local' => '/library/bootstrap/css/bootstrap.min.css',
                    'cdn' => '//stackpath.bootstrapcdn.com/bootstrap/5.0.2/css/bootstrap.min.css',
                ],
                'attributes' => [
                    'integrity' => 'sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC',
                    'crossorigin' => 'anonymous',
                ],
            ],
        ],
    ],
];
