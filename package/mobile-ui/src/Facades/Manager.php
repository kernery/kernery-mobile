<?php

namespace Kernery\MobileUi\Facades;

use Illuminate\Support\Facades\Facade;
use Kernery\MobileUi\Manager as ManagerSupport;

class Manager extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return ManagerSupport::class;
    }
}
