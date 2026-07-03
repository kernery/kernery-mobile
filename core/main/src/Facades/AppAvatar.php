<?php

namespace Kernery\Main\Facades;

use Illuminate\Support\Facades\Facade;
use Kernery\Main\Supports\AppAvatar as Avatar;

class AppAvatar extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return Avatar::class;
    }
}
