<?php

namespace Kernery\MobileUi\Listeners;

use Illuminate\Support\Facades\File;

class CoreUpdateMobileUiDB
{
    public function handle(): void
    {
        $mobileUiPath = ui_path();

        if (! File::isDirectory($mobileUiPath)) {
            return;
        }

        $mobileUiMigrationPath = $mobileUiPath . '/database/migrations';

        if (! File::isDirectory($mobileUiMigrationPath)) {
            return;
        }

        app('migrator')->run($mobileUiMigrationPath);
    }
}
