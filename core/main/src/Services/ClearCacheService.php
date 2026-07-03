<?php

namespace Kernery\Main\Services;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;

class ClearCacheService
{
    public function __construct(protected Application $app, protected Filesystem $files) {}

    public static function make(): self
    {
        return app(self::class);
    }

    public function clearFrameworkCache(): void
    {
        Event::dispatch('cache:clearing');

        Cache::flush();

        Event::dispatch('cache:cleared');
    }

    public function clearBootstrapCache(): void
    {
        foreach ($this->files->glob($this->app->bootstrapPath('cache/*.php')) as $view) {
            $this->files->delete($view);
        }
    }

    public function clearCompiledViews(): void
    {
        foreach ($this->files->glob(config('view.compiled') . '/*.php') as $view) {
            $this->files->delete($view);
        }
    }

    public function clearConfig(): void
    {
        $this->files->delete(App::getCachedConfigPath());
    }

    public function clearRoutesCache(): void
    {
        foreach ($this->files->glob($this->app->bootstrapPath('cache/*')) as $cacheFile) {
            if (Str::contains($cacheFile, 'cache/routes-v7')) {
                $this->files->delete($cacheFile);
            }
        }
    }

    public function clearLogs(): void
    {
        if (! $this->files->isDirectory($logPath = storage_path('logs'))) {
            return;
        }

        foreach ($this->files->glob($logPath . '/*.log') as $file) {
            $this->files->delete($file);
        }
    }

    public function clearPackagesCache(): void
    {
        $this->files->delete(App::getCachedPackagesPath());
    }

    public function clearServicesCache(): void
    {
        $this->files->delete(App::getCachedServicesPath());
    }

    public function clearPurifier(): void
    {
        $purifierPath = config('purifier.cachePath');

        if (! $purifierPath || ! $this->files->isDirectory($purifierPath)) {
            return;
        }

        $this->files->deleteDirectories($purifierPath);
    }

    public function clearDebugbar(): void
    {
        $debugbarPath = config('debugbar.storage.path');

        if (! $debugbarPath || ! $this->files->isDirectory($debugbarPath)) {
            return;
        }

        $this->files->deleteDirectories($debugbarPath);
    }

    public function purgeAll(): void
    {
        $this->clearFrameworkCache();
        $this->clearCompiledViews();
        $this->clearBootstrapCache();
        $this->clearRoutesCache();
        $this->clearConfig();
        $this->clearLogs();
        $this->clearPurifier();
        $this->clearDebugbar();
    }
}
