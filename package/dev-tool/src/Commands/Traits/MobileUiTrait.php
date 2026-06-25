<?php

namespace Kernery\DevTool\Commands\Traits;

trait MobileUiTrait
{
    protected function getPath(?string $path = null, ?string $mobileUi = null): string
    {
        $rootPath = ui_path();
        if ($this->option('path')) {
            $rootPath = $this->option('path');
        }

        if (! $mobileUi) {
            $mobileUi = $this->getMobileUi();
        }

        return rtrim($rootPath, '/') . '/' . rtrim(ltrim(strtolower($mobileUi), '/'), '/') . '/' . $path;
    }

    protected function getMobileUi(): string
    {
        if ($this->hasArgument('name')) {
            return strtolower($this->argument('name'));
        }

        return strtolower($this->option('name'));
    }
}
