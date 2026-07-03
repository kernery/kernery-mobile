<?php

namespace Kernery\Setting\Models;

use Kernery\Main\Models\AppModel;

class Setting extends AppModel
{
    protected $table = 'settings';

    protected $fillable = [
        'key',
        'value',
    ];
}
