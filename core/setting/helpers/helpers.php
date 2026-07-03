<?php

use Kernery\Setting\Facades\Setting;
use Kernery\Setting\Supports\SecureSetting;

if (! function_exists('setting')) {
    function setting(?string $key = null, $default = null)
    {
        if (! empty($key)) {
            try {
                return app(SecureSetting::class)->get($key, $default);
            } catch (Throwable) {
                return $default;
            }
        }

        return Setting::getFacadeRoot();
    }
}
