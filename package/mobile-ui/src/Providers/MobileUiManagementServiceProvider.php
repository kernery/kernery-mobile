<?php

namespace Kernery\MobileUi\Providers;

use Composer\Autoload\ClassLoader;
use Illuminate\Support\Arr;
use Kernery\Main\Facades\MainHelper;
use Kernery\Main\Supports\AppHelper;
use Kernery\Main\Supports\ServiceProvider;
use Kernery\MobileUi\Facades\MobileUi;

class MobileUiManagementServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if (MobileUi::hasInheritMobileUi()) {
            $this->loadJsonTranslationsFromMobileUi(
                MobileUi::getInheritMobileUi()
            );
        }

        $this->loadJsonTranslationsFromMobileUi(
            MobileUi::getMobileUiName()
        );
    }

    public function boot(): void
    {
        $this->registerAutoloadPathFromMobileUi(
            MobileUi::getMobileUiName()
        );
    }

    protected function loadJsonTranslationsFromMobileUi(string $mobileUi): void
    {
        $this->loadJsonTranslationsFrom(ui_path($mobileUi . '/lang'));
        $this->loadJsonTranslationsFrom(lang_path('vendor/mobileUi/' . $mobileUi));
    }

    protected function registerAutoloadPathFromMobileUi(string $mobileUi): void
    {
        if (empty($mobileUi)) {
            return;
        }

        $mobileUiPath = ui_path($mobileUi);

        $configFilePath = $mobileUiPath . '/mobile-ui.json';

        if (app('files')->exists($configFilePath)) {

            $content = MainHelper::getFileData($configFilePath);

            if (! empty($content) && Arr::has($content, 'namespace')) {
                $loader = new ClassLoader;
                $loader->setPsr4($content['namespace'], ui_path($mobileUi . '/src'));
                $loader->register();
            }
        }

        AppHelper::autoload($mobileUiPath . '/utils');
    }
}
