<?php

namespace Kernery\Main\Facades;

use Illuminate\Support\Facades\Facade;
use Kernery\Main\Helpers\MainHelper as SupportsMainHelper;

class MainHelper extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SupportsMainHelper::class;
    }
}
