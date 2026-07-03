<?php

namespace Kernery\MobileUi\Providers;

use Illuminate\Foundation\AliasLoader;
use Kernery\Main\Supports\ServiceProvider;
use Kernery\Main\Traits\LoadAndPublishDataTrait;
use Kernery\MobileUi\Contracts\MobileUi as MobileUiContract;
use Kernery\MobileUi\Facades\MobileUi;

class MobileUiServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->app->singleton(MobileUiContract::class, MobileUi::class);

        $this->prepareAliasesIfMissing();

        // caused an overload for watchman
        // config()->set(['nativephp.hot_reload.watch_paths' => array_merge(config('nativephp.hot_reload.watch_paths', []), ['mobile/ui'])]);

        config()->set('nativephp.hot_reload.watch_paths', ['mobile/ui']);

    }

    public function boot(): void
    {
        $this
            ->setNamespace('package/mobile-ui')
            ->loadAndPublishConfigs(['general'])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadHelpers()
            ->publishAssets();

        $this->registerAutoloadPathForInertia(
            MobileUi::getMobileUiName()
        );

        $this->loadViewsFrom($this->getPath('/resources'), 'mobile-ui');

        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
        });

        $this->app->register(MobileUiManagementServiceProvider::class);
        $this->app->register(EventServiceProvider::class);

    }

    protected function prepareAliasesIfMissing(): void
    {
        $aliasLoader = AliasLoader::getInstance();

        if (! class_exists('MobileUi')) {
            $aliasLoader->alias('MobileUi', MobileUi::class);
        }
    }

    protected function registerAutoloadPathForInertia(string $mobileUi): void
    {
        if (empty($mobileUi)) {
            return;
        }

        $mobileUiPath = 'source/ui/'.$mobileUi.'/views/pages';

        config()->set(['inertia.pages.paths' => array_merge(config('inertia.pages.paths', []), [$mobileUiPath])]);

    }
}
