<?php

namespace Kernery\MobileUi\Listeners;

use Illuminate\Support\Facades\File;
use Kernery\MobileUi\Facades\MobileUi;
use Kernery\MobileUi\Services\MobileUiService;

class PublishMobileUiAssets
{
    public function handle(): void
    {

        $customCSS = MobileUi::getStyleIntegrationPath();

        if (File::exists($customCSS)) {
            File::copy($customCSS, storage_path('app/style.integration.css.') . time());
        }

        app(MobileUiService::class)->publishAssets();
    }
}
