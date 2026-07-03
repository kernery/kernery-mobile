<?php

namespace Kernery\Main\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Kernery\Main\Http\Responses\MainHttpResponse;
use Symfony\Component\HttpFoundation\Response;

class RequiresJsonRequestMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->expectsJson()) {
            return MainHttpResponse::make()->setNextUrl(url('/'));
        }

        return $next($request);
    }
}
