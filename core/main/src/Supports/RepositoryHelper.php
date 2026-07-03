<?php

namespace Kernery\Main\Supports;

class RepositoryHelper
{
    public static function applyBeforeExecuteQuery($data, $model, bool $isSingle = false)
    {
        $filter = $isSingle ? APP_FILTER_BEFORE_GET_SINGLE : APP_FILTER_BEFORE_GET_FRONT_PAGE_ITEM;

        return apply_filters($filter, $data, $model);
    }
}
