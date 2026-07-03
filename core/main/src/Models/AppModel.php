<?php

namespace Kernery\Main\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Kernery\Main\Contracts\MainModel as AppModelContract;
use Kernery\Main\Facades\MacroableModels;
use Kernery\Main\Models\Concerns\HasMainEloquentBuilder;
use Kernery\Main\Models\Concerns\HasUuidsOrIntegerIds;

/**
 * @method static \Kernery\Main\Models\AppQueryBuilder query()
 */
class AppModel extends Model implements AppModelContract
{
    use HasMainEloquentBuilder;
    use HasUuidsOrIntegerIds;

    public function __get($key)
    {
        if (MacroableModels::modelHasMacro($this::class, $method = 'get' . Str::studly($key) . 'Attribute')) {
            return call_user_func([$this, $method]);
        }

        return parent::__get($key);
    }
}
