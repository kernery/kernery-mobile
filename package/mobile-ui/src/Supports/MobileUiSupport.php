<?php

namespace Kernery\MobileUi\Supports;

class MobileUiSupport
{
    public static function getMobileUiIcons(): array
    {
        return apply_filters(MOBILE_UI_LIST_ICONS, []);
    }
}
