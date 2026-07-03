<?php

namespace Kernery\MobileUi\Http\Controllers;

use Kernery\Main\Http\Controllers\MainController;
use Kernery\MobileUi\Events\RenderingHomePageEvent;
use Kernery\MobileUi\Facades\MobileUi;

class PublicController extends MainController
{
    public function getIndex()
    {
        MobileUi::addBodyAttributes(['id' => 'app']);

        if (config('package.mobile-ui.general.apply_safe_area_in_mobile', true)) {

            MobileUi::addBodyAttributes(['class' => 'nativephp-safe-area']);

        }

        event(RenderingHomePageEvent::class);

        return MobileUi::scope('index')->withDebugBadge()->render();
    }
}
