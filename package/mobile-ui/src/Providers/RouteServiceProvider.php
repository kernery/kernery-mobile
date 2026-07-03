<?php

namespace Kernery\MobileUi\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Kernery\MobileUi\Facades\MobileUi;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Move base routes to a service provider to make sure all filters & actions can hook to base routes
     */
    public function boot(): void
    {
        $this->app->booted(function () {
            if (MobileUi::hasInheritMobileUi()) {
                $this->loadRoutesFromMobileUi(MobileUi::getInheritMobileUi());
            }

            $this->loadRoutesFromMobileUi(MobileUi::getMobileUiName());
        });
    }

    protected function loadRoutesFromMobileUi(string $mobileUi): void
    {
        $routeFilePath = ui_path($mobileUi . '/routes/web.php');

        if (app('files')->exists($routeFilePath)) {
            $this->loadRoutesFrom($routeFilePath);
        }
    }
}
