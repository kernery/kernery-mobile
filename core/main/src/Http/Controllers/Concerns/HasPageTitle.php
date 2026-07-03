<?php

namespace Kernery\Main\Http\Controllers\Concerns;

use Kernery\Main\Facades\PageTitle;

trait HasPageTitle
{
    protected function pageTitle(string $title): void
    {
        PageTitle::setTitle($title);

    }

    protected function getPageTitle($withAppName = true): string
    {
        return PageTitle::getTitle($withAppName);
    }
}
