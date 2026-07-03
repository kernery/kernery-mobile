<?php

use Illuminate\Support\Facades\Route;
use Kernery\MobileUi\Events\MobileUiRoutingAfterEvent;
use Kernery\MobileUi\Events\MobileUiRoutingBeforeEvent;
use Kernery\MobileUi\Facades\MobileUi;
use Kernery\MobileUi\Http\Controllers\PublicController;

MobileUi::registerRoutes(function () {

    Route::group(['controller' => PublicController::class], function () {

        event(new MobileUiRoutingBeforeEvent(app()->make('router')));

        Route::get('/', 'getIndex')->name('public.index');

        event(new MobileUiRoutingAfterEvent(app()->make('router')));
    });
});
