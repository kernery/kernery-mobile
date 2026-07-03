<?php

use Kernery\MobileUi\Contracts\MobileUi as MobileUiContract;
use Kernery\MobileUi\Supports\Template;

if (! function_exists('mobile_ui')) {
    function mobile_ui(?string $mobileUiName = null, ?string $layoutName = null): mixed
    {
        $mobileUi = app(MobileUiContract::class);

        if ($mobileUiName) {
            $mobileUi->mobileUi($mobileUiName);
        }

        if ($layoutName) {
            $mobileUi->layout($layoutName);
        }

        return $mobileUi;
    }
}

if (! function_exists('ui_path')) {
    function ui_path(?string $path = null): string
    {
        return mobile_path('ui' . DIRECTORY_SEPARATOR . ltrim((string) $path, DIRECTORY_SEPARATOR));
    }
}

if (! function_exists('register_page_template')) {
    function register_page_template(array $templates): void
    {
        Template::registerPageTemplate($templates);
    }
}
