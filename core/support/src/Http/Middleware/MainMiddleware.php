<?php

namespace Kernery\Support\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MainMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}
