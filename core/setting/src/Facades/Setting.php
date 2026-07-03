<?php

namespace Kernery\Setting\Facades;

use Illuminate\Support\Facades\Facade;
use Kernery\Setting\Supports\SecureSetting;

class Setting extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SecureSetting::class;
    }
}
