<?php

namespace Kernery\Main\Helpers;

use Closure;
use Illuminate\Routing\RouteRegistrar;
use Illuminate\Support\Facades\Route;

class MiscHelper
{
    public function registerRoutes(
        Closure | callable $closure,
        array $middleware = ['web', 'core']
    ): RouteRegistrar {
        return Route::middleware($middleware)->group(fn () => $closure());
    }
}
