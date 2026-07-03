<?php

namespace Kernery\Main\Facades;

use Illuminate\Support\Facades\Facade;
use Kernery\Main\Helpers\MiscHelper as MiscSupport;

class MiscHelper extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MiscSupport::class;
    }
}
