<?php

namespace Kernery\Main\Facades;

use Illuminate\Support\Facades\Facade;
use Kernery\Main\Supports\Action as SupportsAction;

class Action extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SupportsAction::class;
    }
}
