<?php

namespace Kernery\Assets\Providers;

use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\ServiceProvider;
use Kernery\Main\Facades\Assets;
use Kernery\Main\Traits\LoadAndPublishDataTrait;

class AssetServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function boot(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/assets.php', 'assets');
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'assets');

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../../config/assets.php' => config_path('assets.php')], 'config');
            $this->publishes([__DIR__ . '/../../resources/views' => resource_path('views/vendor/assets')], 'views');
        }
    }

    public function register(): void
    {
        // Register class path and alias loader for missing facade
        $this->prepareAliasesIfMissing();
    }

    protected function prepareAliasesIfMissing(): void
    {
        $aliasLoader = AliasLoader::getInstance();

        if (! class_exists('Assets')) {
            $aliasLoader->alias('Assets', Assets::class);
        }
    }
}
