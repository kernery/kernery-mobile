<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Mobile Default
    |--------------------------------------------------------------------------
    |
    | If you don't set a mobile ui when using a "Mobile" class the default mobile
    | will replace automatically.
    |
    */

    'mobileUiDefault' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Layout Default
    |--------------------------------------------------------------------------
    |
    | If you don't set a layout when using a "Mobile" class the default layout
    | will replace automatically.
    |
    */

    'layoutDefault' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Path to lookup mobile ui
    |--------------------------------------------------------------------------
    |
    | The root path contains ui collections.
    |
    */

    'mobileUiDir' => 'ui',

    /*
    |--------------------------------------------------------------------------
    | A pieces of mobile ui collections
    |--------------------------------------------------------------------------
    |
    | Inside a mobile ui path we need to set up directories to
    | keep "layouts", "assets" and "partials".
    |
    */

    'containerDir' => [
        'layout' => 'views/layouts',
        'asset' => '',
        'partial' => 'views/partials',
        'view' => 'views',
    ],

    /*
    |--------------------------------------------------------------------------
    | Listener from events
    |--------------------------------------------------------------------------
    |
    | You can hook a mobile when event fired on activities
    | this is cool feature to set up a title, meta, default styles and scripts.
    |
    */

    'events' => [],

    'public_mobile_ui_name' => env('APP_MOBILE_UI_PUBLIC_NAME'),

    'templates' => [
        'default' => 'Default',
    ],

    'apply_safe_area_in_mobile' => env('APPLY_SAFE_AREA_IN_MOBILE', false),
];
