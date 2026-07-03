<?php

namespace Kernery\Main\Services;

use Illuminate\Support\Facades\Artisan;

class AppMigrationService
{
    protected array $paths = [];

    public function __construct()
    {
        $this->paths = [
            core_path('setting/database/migrations'),
        ];
    }

    public function runMigrations(): void
    {
        foreach ($this->paths as $path) {
            if (is_dir($path)) {
                Artisan::call('migrate', [
                    '--path' => $path,
                    '--realpath' => true,
                    '--force' => true,
                ]);
            }
        }
    }
}
