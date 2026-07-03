<?php

namespace Kernery\Support\Providers;

use Kernery\Main\Supports\ServiceProvider;
use Kernery\Main\Traits\LoadAndPublishDataTrait;

class SupportServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register(): void
    {
        $this->setNamespace('core/support')
            ->loadHelpers();
    }

    public function boot(): void
    {
        $this->app['files']->requireOnce(core_path('support/helpers/common.php'));
    }
}
