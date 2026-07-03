<?php

namespace Kernery\MobileUi\Listeners;

use Kernery\MobileUi\Facades\MobileUi;
use Kernery\Setting\Facades\Setting;

class SetDefaultMobileUi
{
    public function handle(): void
    {
        Setting::forceSet('mobile-ui', MobileUi::getMobileUiName() ?? 'default');
    }
}
