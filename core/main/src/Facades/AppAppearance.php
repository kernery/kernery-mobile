<?php

namespace Kernery\Main\Facades;

use Illuminate\Support\Facades\Facade;
use Kernery\Main\Supports\AppAppearance as CoreAppAppearance;

class AppAppearance extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return CoreAppAppearance::class;
    }
}
