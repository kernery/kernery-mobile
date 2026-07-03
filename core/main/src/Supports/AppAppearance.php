<?php

namespace Kernery\Main\Supports;

class AppAppearance
{
    public function getLocale(): string
    {
        $generalLocale = config('core.main.general.locale');

        return $this->$generalLocale;
    }
}
