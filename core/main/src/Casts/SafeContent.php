<?php

namespace Kernery\Main\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Kernery\Main\Facades\MainHelper;

class SafeContent implements CastsAttributes
{
    public function set($model, string $key, $value, array $attributes)
    {
        return MainHelper::clean($value);
    }

    public function get($model, string $key, $value, array $attributes)
    {
        return html_entity_decode(MainHelper::clean($value));
    }
}
