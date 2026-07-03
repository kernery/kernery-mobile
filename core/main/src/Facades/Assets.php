<?php

namespace Kernery\Main\Facades;

use Illuminate\Support\Facades\Facade;
use Kernery\Main\Supports\Assets as AppAssets;

class Assets extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return AppAssets::class;
    }
}
