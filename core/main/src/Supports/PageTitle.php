<?php

namespace Kernery\Main\Supports;

class PageTitle
{
    protected string $title;

    protected string $appName;

    public function setAppName(string $appName): void
    {
        $this->appName = $appName;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title ?? $this->appName;
    }

    public function getTitle(bool $withAppName = true): ?string
    {
        $appName = $this->appName ?? config('core.main.general.project_name');

        if (empty($this->title)) {
            return $appName;
        }

        if (! $withAppName) {
            return $this->title;
        }

        return $appName;
    }
}
