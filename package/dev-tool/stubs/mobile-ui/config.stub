<?php

use Kernery\MobileUi\MobileUi;

return [

    /*
    |--------------------------------------------------------------------------
    | Inherit from another mobileUi
    |--------------------------------------------------------------------------
    */

    'inherit' => null, //default

    /*
    |--------------------------------------------------------------------------
    | Listener from events
    |--------------------------------------------------------------------------
    |
    | You can hook a mobile ui when event fired on activities
    | this is cool feature to set up a title, meta, default styles and scripts.
    |
    | [Notice] these events can be overridden by package config.
    |
    */

    'events' => [

        // Before event inherit from package config and the mobile ui that call before,
        // you can use this event to set meta, template or anything
        // you want inheriting.
        'before' => function ($mobileUi): void {
            // You can remove this line anytime.
        },

        // Listen on event before render a mobile ui,
        // this event should call to assign some assets,
        // breadcrumb template.
        'beforeRenderMobileUi' => function (MobileUi $mobileUi): void {
            // Partial composer.
            // $mobileUi->partialComposer('header', function($view) {
            //     $view->with('auth', \Auth::user());
            // });

            // You may use this event to set up your assets.

            $version = get_app_version();

            // Change arguement to true if you intend to use vite to build your assets
            // Do not forget to change public path to the example below to use ui path in your build tool
            // Ex. $mobileUi->asset()->container('header')->addUsingPublicPath('style', 'public/css/style.css', [], true, version: $version);

            $mobileUi->asset()->container('header')->addUsingPublicPath('style', 'css/style.css', [], false, version: $version);

            $mobileUi->asset()->container('footer')->addUsingPublicPath('script', 'js/script.js', [], false, version: $version);
        },

        // Listen on event before render a layout,
        // this should call to assign style, script for a layout.
        'beforeRenderLayout' => [
            'default' => function ($mobileUi): void {
                // $mobileUi->asset()->usePath()->add('ipad', 'css/layouts/ipad.css');
            },
        ],
    ],
];
