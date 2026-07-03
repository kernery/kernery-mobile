<?php

namespace Kernery\Main\Facades;

use Illuminate\Support\Facades\Facade;
use Kernery\Main\Supports\MacroableModels as MacroableModelsSupport;

class MacroableModels extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return MacroableModelsSupport::class;
    }
}
