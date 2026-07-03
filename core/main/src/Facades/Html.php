<?php

namespace Kernery\Main\Facades;

use Illuminate\Support\Facades\Facade;
use Kernery\Main\Supports\HtmlBuilder;

class Html extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return HtmlBuilder::class;
    }
}
