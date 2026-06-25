<?php

namespace Kernery\Assets\Facades;

use Illuminate\Support\Facades\Facade;
use Kernery\Assets\Assets;

class AssetsFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Assets::class;
    }
}
