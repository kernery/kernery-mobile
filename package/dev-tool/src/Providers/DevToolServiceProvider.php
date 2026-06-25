<?php

namespace Kernery\DevTool\Providers;

use Kernery\Main\Supports\ServiceProvider;

class DevToolServiceProvider extends ServiceProvider
{
    public function boot(): void
    {

        $this->app->register(CommandServiceProvider::class);
    }
}
