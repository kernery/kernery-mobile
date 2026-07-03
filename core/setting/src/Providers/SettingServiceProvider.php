<?php

namespace Kernery\Setting\Providers;

use Illuminate\Foundation\AliasLoader;
use Kernery\Main\Supports\ServiceProvider;
use Kernery\Main\Traits\LoadAndPublishDataTrait;
use Kernery\Setting\Facades\Setting;
use Kernery\Setting\Models\Setting as SettingModel;
use Kernery\Setting\Repositories\Eloquent\SettingRepository;
use Kernery\Setting\Repositories\Interfaces\SettingInterface;
use Kernery\Setting\Supports\DatabaseSettingStore;
use Kernery\Setting\Supports\SecureSetting;

class SettingServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    protected bool $defer = true;

    public function register(): void
    {
        $this
            ->setNamespace('core/setting')
            ->loadAndPublishConfigs(['general']);

        $this->app->singleton(Setting::class, function () {
            return new DatabaseSettingStore;
        });

        $this->app->singleton(SecureSetting::class, function ($app) {
            return $app->make(Setting::class);
        });

        $this->app->bind(SettingInterface::class, function () {
            return new SettingRepository(new SettingModel);
        });

        if (! class_exists('Setting')) {
            AliasLoader::getInstance()->alias('Setting', Setting::class);
        }

        $this->loadHelpers();
    }

    public function provides(): array
    {
        return [
            SecureSetting::class,
        ];
    }
}
