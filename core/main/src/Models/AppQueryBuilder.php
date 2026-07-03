<?php

namespace Kernery\Main\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Kernery\Main\Enums\MainStatusEnum;

class AppQueryBuilder extends Builder
{
    protected function getBackslashByPdo(): string
    {
        if (DB::getDefaultConnection() === 'sqlite') {
            return '\\\\';
        }

        return '\\\\\\';
    }

    public function wherePublished($column = 'status'): static
    {
        $this->where($column, MainStatusEnum::PUBLISHED);

        return $this;
    }

    public function get($columns = ['*'])
    {
        $data = parent::get($columns);

        return apply_filters('model_after_execute_get', $data, $this->getModel(), $columns);
    }
}
