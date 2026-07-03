<?php

namespace Kernery\Main\Supports;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use Kernery\Main\Services\ClearCacheService;
use Throwable;

class AppHelper
{
    public static function autoload(string $directory): void
    {
        $helpers = File::glob($directory . '/*.php');

        if (empty($helpers) || ! is_array($helpers)) {
            return;
        }

        foreach ($helpers as $helper) {
            File::requireOnce($helper);
        }
    }

    public static function formatLog(array $input, string $line = '', string $function = '', string $class = ''): array
    {
        return array_merge($input, [
            'user_id' => Auth::guard()->check() ? Auth::guard()->id() : 'System',
            'ip' => Request::ip(),
            'line' => $line,
            'function' => $function,
            'class' => $class,
            'userAgent' => Request::header('User-Agent'),
        ]);
    }

    public static function isConnectedDatabase(): bool
    {
        try {
            return Schema::hasTable('settings');
        } catch (Throwable) {
            return false;
        }
    }

    public static function getIpFromThirdParty(): bool | string | null
    {
        $defaultIpAddress = Request::ip() ?: '127.0.0.1';

        try {
            $ip = trim(Http::withoutVerifying()->get('https://ipecho.net/plain')->body());

            return filter_var($ip, FILTER_VALIDATE_IP) ? $ip : $defaultIpAddress;

        } catch (Throwable) {
            return $defaultIpAddress;
        }
    }

    public static function isIniValueChangeable(string $setting): bool
    {
        static $iniAll;

        if (! isset($iniAll)) {
            $iniAll = false;

            // Sometimes `ini_get_all()` is disabled via the `disable_functions` option for "security purposes".
            if (function_exists('ini_get_all')) {
                $iniAll = ini_get_all();
            }
        }

        // Bit operator to workaround https://bugs.php.net/bug.php?id=44936 which changes access level to 63 in PHP 5.2.6 - 5.2.17.
        if (isset($iniAll[$setting]['access']) && (INI_ALL === ($iniAll[$setting]['access'] & 7) || INI_USER === ($iniAll[$setting]['access'] & 7))) {
            return true;
        }

        // If we were unable to retrieve the details, fail gracefully to assume it's changeable.
        if (! is_array($iniAll)) {
            return true;
        }

        return false;
    }

    public static function convertHrToBytes(string | float | int | null $value): float | int
    {
        $value = strtolower(trim($value));
        $bytes = (int) $value;

        if (str_contains($value, 'g')) {
            $bytes *= 1024 * 1024 * 1024;
        } elseif (str_contains($value, 'm')) {
            $bytes *= 1024 * 1024;
        } elseif (str_contains($value, 'k')) {
            $bytes *= 1024;
        }

        // Deal with large (float) values which run into the maximum integer size.
        return min($bytes, PHP_INT_MAX);
    }

    public static function clearCache(): bool
    {
        $clearCacheService = ClearCacheService::make();

        $clearCacheService->clearFrameworkCache();
        $clearCacheService->clearBootstrapCache();
        $clearCacheService->clearRoutesCache();
        $clearCacheService->clearPurifier();
        $clearCacheService->clearDebugbar();

        return true;
    }
}
