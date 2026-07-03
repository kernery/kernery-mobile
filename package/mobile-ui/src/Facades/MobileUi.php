<?php

namespace Kernery\MobileUi\Facades;

use Illuminate\Support\Facades\Facade;
use Kernery\MobileUi\MobileUi as MainMobileUi;

class MobileUi extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MainMobileUi::class;
    }
}
