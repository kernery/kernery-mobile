<?php

namespace Kernery\DevTool\Providers;

use Kernery\DevTool\Commands\CleanupDatabaseCommand;
use Kernery\DevTool\Commands\InstallCommand;
use Kernery\DevTool\Commands\MobileUiActivateCommand;
use Kernery\DevTool\Commands\MobileUiAssetsPublishCommand;
use Kernery\DevTool\Commands\MobileUiAssetsRemoveCommand;
use Kernery\DevTool\Commands\MobileUiCreateCommand;
use Kernery\DevTool\Commands\MobileUiRemoveCommand;
use Kernery\DevTool\Commands\MobileUiRenameCommand;
use Kernery\DevTool\Commands\ViteConfigPublishCommand;
use Kernery\Main\Supports\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MobileUiActivateCommand::class,
                CleanupDatabaseCommand::class,
                MobileUiRemoveCommand::class,
                MobileUiAssetsPublishCommand::class,
                MobileUiAssetsRemoveCommand::class,
                MobileUiRenameCommand::class,
                MobileUiCreateCommand::class,
                InstallCommand::class,
                ViteConfigPublishCommand::class,
            ]);
        }
    }
}
