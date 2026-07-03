<?php

use Illuminate\Support\Facades\Route;
use Kernery\Main\Facades\MiscHelper;

Route::group(['namespace' => 'Kernery\Main\Http\Controllers'], function () {
    MiscHelper::registerRoutes(function () {
        Route::get('app/check-update', [
            'as' => 'app.check-update',
            'uses' => 'AppController@getCheckUpdate',
        ]);
    });
});
