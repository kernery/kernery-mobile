<?php

namespace Kernery\MobileUi;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Kernery\Main\Facades\MainHelper;
use Kernery\MobileUi\Facades\MobileUi as MobileUiFacade;
use Kernery\MobileUi\Services\MobileUiService;

class Manager
{
    protected array $mobileUis = [];

    public function __construct(protected MobileUiService $mobileUiService)
    {
        $this->registerMobileUi(self::getAllMobileUis());
    }

    public function registerMobileUi(string | array $mobileUi): void
    {
        if (! is_array($mobileUi)) {
            $mobileUi = [$mobileUi];
        }

        $this->mobileUis = array_merge_recursive($this->mobileUis, $mobileUi);
    }

    public function getAllMobileUis(): array
    {
        $mobileUis = [];

        $mobileUiPath = ui_path();

        foreach (MainHelper::scanFolder($mobileUiPath) as $folder) {

            $jsonFile = $mobileUiPath . '/' . $folder . '/mobile-ui.json';

            $publicJsonFile = public_path('ui/' . MobileUiFacade::getPublicMobileUiName() . '/mobile-ui.json');

            if (File::exists($publicJsonFile)) {
                $jsonFile = $publicJsonFile;
            }

            if (! File::exists($jsonFile)) {
                continue;
            }

            $mobileUi = MainHelper::getFileData($jsonFile);

            if (! empty($mobileUi)) {

                $mobileUiConfig = $this->mobileUiService->getmobileUiConfig($folder);

                $mobileUis[$folder] = $mobileUi;

                $mobileUis[$folder]['inherit'] = Arr::get($mobileUiConfig, 'inherit');
            }
        }

        return $mobileUis;
    }

    public function getMobileUis(): array
    {
        return $this->mobileUis;
    }
}
