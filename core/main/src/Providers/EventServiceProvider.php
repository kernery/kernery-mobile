<?php

namespace Kernery\Main\Providers;

use Illuminate\Database\Events\MigrationsStarted;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Routing\Events\RouteMatched;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Kernery\Main\Events\BeforeEditContentEvent;
use Kernery\Main\Events\CreatedContentEvent;
use Kernery\Main\Events\DeletedContentEvent;
use Kernery\Main\Events\UpdatedContentEvent;
use Kernery\Main\Http\Middleware\CoreMiddleware;
use Kernery\Main\Listeners\BeforeEditContentListener;
use Kernery\Main\Listeners\CreatedContentListener;
use Kernery\Main\Listeners\DeletedContentListener;
use Kernery\Main\Listeners\UpdatedContentListener;
use Kernery\Support\Http\Middleware\MainMiddleware;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        CreatedContentEvent::class => [
            CreatedContentListener::class,
        ],
        UpdatedContentEvent::class => [
            UpdatedContentListener::class,
        ],
        DeletedContentEvent::class => [
            DeletedContentListener::class,
        ],
        BeforeEditContentEvent::class => [
            BeforeEditContentListener::class,
        ],
    ];

    public function boot(): void
    {
        $events = app('events');

        $events->listen(RouteMatched::class, function () {

            $router = app('router');

            $router->middlewareGroup('core', [CoreMiddleware::class]);

            $this->disableCsrfProtection();
        });

        $events->listen(MigrationsStarted::class, function () {
            rescue(function () {
                if (DB::getDefaultConnection() === 'sqlite') {
                    DB::statement('SET SESSION sql_require_primary_key=0');
                }
            }, report: false);
        });

        $events->listen(['cache:cleared'], function () {
            app('files')->delete(storage_path('cache_keys.json'));
        });

        if (app()->environment('local')) {
            DB::listen(function (QueryExecuted $queryExecuted) {
                if ($queryExecuted->time < 500) {
                    return;
                }

                Log::warning(sprintf('DB query exceeded %s ms. SQL: %s', $queryExecuted->time, $queryExecuted->sql));
            });
        }
    }

    protected function disableCsrfProtection(): void
    {
        $config = app('config');

        if ($config->get('core.main.general.disable_verify_csrf_token', false)
            || ($this->app->environment('production'))
        ) {

            $this->app->instance(PreventRequestForgery::class, new MainMiddleware);
        }
    }
}
