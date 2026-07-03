<?php

use Kernery\Main\Supports\AppCore;

if (! function_exists('mobile_path')) {
    function mobile_path(?string $path = null): string
    {
        $path = ltrim($path, DIRECTORY_SEPARATOR);

        return base_path('mobile' . ($path ? DIRECTORY_SEPARATOR . $path : ''));
    }
}

if (! function_exists('core_path')) {
    function core_path(?string $path = null): string
    {
        return mobile_path('core' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : ''));
    }
}

if (! function_exists('package_path')) {
    function package_path(?string $path = null): string
    {
        return mobile_path('package' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : ''));
    }
}

if (! function_exists('db_path')) {
    function db_path(?string $path = null): string
    {
        return mobile_path('database' . ($path ? DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : ''));
    }
}

if (! function_exists('get_app_version')) {
    function get_app_version(): string
    {
        try {
            return AppCore::make()->version();
        } catch (Throwable) {
            return '0.0.0';
        }
    }
}
