<?php

use Kernery\MobileUi\Facades\MobileUi;
use Illuminate\Support\Facades\Route;

// Custom routes
// You can delete this route group if you don't need to add your custom routes.

MobileUi::registerRoutes(function (): void {
    // Add your custom route here
    // Example: 
    // Route::get('hello', 'getHello');
});

MobileUi::routes();
