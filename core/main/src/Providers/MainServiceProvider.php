<?php

namespace Kernery\Main\Providers;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Database\Schema\Builder;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Foundation\Application;
use Illuminate\Routing\ResourceRegistrar;
use Illuminate\Routing\Route;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Kernery\Main\Exceptions\Handler;
use Kernery\Main\Facades\MainHelper;
use Kernery\Main\Facades\PageTitle;
use Kernery\Main\Models\AppModel;
use Kernery\Main\Supports\Action;
use Kernery\Main\Supports\AppAvatar;
use Kernery\Main\Supports\AppHelper;
use Kernery\Main\Supports\CustomResourceRegistrar;
use Kernery\Main\Supports\Database\Blueprint;
use Kernery\Main\Supports\Filter;
use Kernery\Main\Supports\ServiceProvider;
use Kernery\Main\Traits\LoadAndPublishDataTrait;
use Kernery\Setting\Providers\SettingServiceProvider;
use Kernery\Setting\Supports\SecureSetting;

class MainServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this
            ->setNamespace('core/main')
            ->loadAndPublishConfigs('general')
            ->loadHelpers();

        $this->app->instance('core.middleware', []);

        $this->app->bind(ResourceRegistrar::class, function (Application $app) {
            return new CustomResourceRegistrar($app['router']);
        });

        $this->app->register(SettingServiceProvider::class);

        $this->app->singleton(ExceptionHandler::class, Handler::class);

        $this->app->singleton('core.action', Action::class);

        $this->app->singleton('core.filter', Filter::class);

        $this->registerRouteMacros();

        $this->prepareAliasesIfMissing();

        config()->set(['session.cookie' => config('core.main.general.project_name')]);

        $this->overrideDefaultConfigs();

        Schema::defaultStringLength(191);
    }

    public function boot(): void
    {
        $this
            ->loadAndPublishConfigs(['assets'])
            ->loadAndPublishViews()
            ->loadAnonymousComponents()
            ->loadAndPublishTranslations()
            ->loadRoutes()
            ->loadMigrations()
            ->publishAssets();

        app('blade.compiler')->anonymousComponentPath($this->getViewsPath() . '/components', 'app');

        $this->overridePackagesConfigs();

        $this->app->booted(function () {
            do_action(INIT_APP_ACTION);
        });

        $this->configureIni();

        $this->app->extend('db.schema', function (Builder $schema) {
            $schema->blueprintResolver(function ($table, $callback, $prefix) {
                return new Blueprint($table, $callback, $prefix);
            });

            return $schema;
        });
    }

    protected function configureIni(): void
    {
        $currentLimit = @ini_get('memory_limit');
        $currentLimitInt = AppHelper::convertHrToBytes($currentLimit);

        $baseConfig = $this->getMainConfig();

        $memoryLimit = Arr::get($baseConfig, 'memory_limit');

        if (! $memoryLimit) {
            if (AppHelper::isIniValueChangeable('memory_limit') === false) {
                $memoryLimit = $currentLimit;
            } else {
                $memoryLimit = '256M';
            }
        }

        $limitInt = AppHelper::convertHrToBytes($memoryLimit);

        if ($currentLimitInt !== -1 && ($limitInt === -1 || $limitInt > $currentLimitInt)) {
            MainHelper::iniSet('memory_limit', $memoryLimit);
        }

        $maxExecutionTime = Arr::get($baseConfig, 'max_execution_time');

        $currentExecutionTimeLimit = @ini_get('max_execution_time');

        if ($currentExecutionTimeLimit < $maxExecutionTime) {
            MainHelper::iniSet('max_execution_time', $maxExecutionTime);
        }
    }

    protected function getMainConfig(): array
    {
        return $this->getConfig()->get('core.main.general', []);
    }

    protected function getConfig(): Repository
    {
        return app('config');
    }

    protected function overrideDefaultConfigs(): void
    {
        $config = $this->getConfig();

        $config->set([
            'app.debug_blacklist' => [
                '_ENV' => [
                    'APP_KEY',
                    'ADMIN_DIR',
                    'DB_DATABASE',
                    'DB_USERNAME',
                    'DB_PASSWORD',
                    'REDIS_PASSWORD',
                    'MAIL_PASSWORD',
                    'PUSHER_APP_KEY',
                    'PUSHER_APP_SECRET',
                ],
                '_SERVER' => [
                    'APP_KEY',
                    'ADMIN_DIR',
                    'DB_DATABASE',
                    'DB_USERNAME',
                    'DB_PASSWORD',
                    'REDIS_PASSWORD',
                    'MAIL_PASSWORD',
                    'PUSHER_APP_KEY',
                    'PUSHER_APP_SECRET',
                ],
                '_POST' => [
                    'password',
                ],
            ],
            'debugbar.enabled' => $this->app->hasDebugModeEnabled() &&
                ! $this->app->runningInConsole() &&
                ! $this->app->environment(['testing', 'production']),
            'debugbar.capture_ajax' => false,
            'debugbar.remote_sites_path' => '',
        ]);

        if (
            ! $config->has('logging.channels.deprecations')
            && $this->app->environment('local')
            && $this->app->hasDebugModeEnabled()
        ) {
            $config->set([
                'logging.channels.deprecations' => [
                    'driver' => 'single',
                    'path' => storage_path('logs/php-deprecation-warnings.log'),
                ],
            ]);
        }

        if (
            $this->app->environment('local')
        ) {
            $config->set([
                'database.default' => 'sqlite',
                'database.connections' => [
                    'sqlite' => [
                        'driver' => 'sqlite',
                        'database' => config('core.main.config.general.default_database_directory', db_path('database.sqlite')),
                    ],
                ],
            ]);
        }

    }

    protected function overridePackagesConfigs(): void
    {
        $config = $this->getConfig();
        /**
         * @var SecureSetting $setting
         */
        $setting = app(SecureSetting::class);

        $baseConfig = $this->getMainConfig();

        $config->set([
            'purifier.settings' => [
                ...$config->get('purifier.settings', []),
                ...Arr::get($baseConfig, 'purifier', []),
            ],
            'ziggy.except' => ['debugbar.*'],
        ]);
    }

    protected function registerRouteMacros(): void
    {
        Route::macro('wherePrimaryKey', function (array | string | null $name = 'id') {
            if (! $name) {
                $name = 'id';
            }

            /**
             * @var Route $this
             */
            if (AppModel::determineIfUsingUuidsForId()) {
                return $this->whereUuid($name);
            }

            if (AppModel::determineIfUsingUlidsForId()) {
                return $this->whereUlid($name);
            }

            return $this->whereNumber($name);
        });
    }

    protected function prepareAliasesIfMissing(): void
    {
        $aliasLoader = AliasLoader::getInstance();

        if (! class_exists('MainHelper')) {
            $aliasLoader->alias('MainHelper', MainHelper::class);
            $aliasLoader->alias('PageTitle', PageTitle::class);
        }

        if (! class_exists('AppHelper')) {
            $aliasLoader->alias('AppHelper', AppHelper::class);
        }
        if (! class_exists('AppAvatar')) {
            $aliasLoader->alias('AppAvatar', AppAvatar::class);
        }
    }
}
