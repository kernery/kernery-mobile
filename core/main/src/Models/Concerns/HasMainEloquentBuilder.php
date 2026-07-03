<?php

namespace Kernery\Main\Models\Concerns;

use Kernery\Main\Models\AppQueryBuilder;

trait HasMainEloquentBuilder
{
    public function newEloquentBuilder($query): AppQueryBuilder
    {
        return new AppQueryBuilder($query);
    }
}
