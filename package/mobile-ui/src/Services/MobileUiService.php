<?php

namespace Kernery\MobileUi\Services;

use Illuminate\Filesystem\Filesystem;
use Kernery\Main\Facades\MainHelper;
use Kernery\Main\Supports\AppHelper;
use Kernery\MobileUi\Events\MobileUiRemoveEvent;
use Kernery\MobileUi\Facades\MobileUi;
use Kernery\Setting\Supports\SecureSetting;

class MobileUiService
{
    public function __construct(
        protected Filesystem $files,
        protected SecureSetting $secureSetting,
    ) {}

    public function activate(string $mobileUi)
    {
        $validate = $this->validate($mobileUi);

        if ($validate['error']) {
            return $validate;
        }

        if (setting('mobile-ui') && $mobileUi == MobileUi::getMobileUiName()) {
            return [
                'error' => true,
                'message' => trans('package/mobile-ui::mobile-ui.mobile-ui_activated_already', ['name' => $mobileUi]),
            ];
        }

        MobileUi::setMobileUiName($mobileUi);

        $published = $this->publishAssets($mobileUi);

        if ($published['error']) {
            return $published;
        }

        $this->secureSetting
            ->forceSet('mobile-ui', $mobileUi)
            ->save();

        AppHelper::clearCache();

        return [
            'error' => false,
            'message' => trans('package/mobile-ui::mobile-ui.active_success', ['name' => $mobileUi]),
        ];
    }

    protected function validate(string $mobileUi): array
    {
        $location = ui_path($mobileUi);

        if (! $this->files->isDirectory($location)) {
            return [
                'error' => false,
                'message' => trans('package/mobile-ui::mobile-ui.mobile-ui_is_not_existed'),
            ];
        }

        if (! $this->files->exists($location . '/mobile-ui.json')) {
            return [
                'error' => true,
                'message' => trans('package/mobile-ui::mobile-ui.missing_json_file'),
            ];
        }

        return [
            'error' => false,
            'message' => trans('package/mobile-ui::mobile-ui.mobile-ui_invalid'),
        ];
    }

    public function getMobileUiConfig(string $mobileUi): array
    {
        $configFile = $this->getPath($mobileUi, 'config.php');

        return $this->files->exists($configFile) ? $this->files->getRequire($configFile) : [];
    }

    protected function getPath(string $mobileUi, ?string $path = null): string
    {
        return rtrim(ui_path(), '/') . '/' . rtrim(ltrim(strtolower($mobileUi), '/'), '/') . '/' . $path;
    }

    public function publishAssets(?string $mobileUi = null): array
    {
        if ($mobileUi) {
            $mobileUis = [$mobileUi];
        } else {
            $mobileUis = MainHelper::scanFolder(ui_path());
        }

        foreach ($mobileUis as $key => $mobileUi) {
            if (! $this->files->isDirectory(ui_path($mobileUi))) {
                unset($mobileUis[$key]);

                continue;
            }

            $resourcePath = $this->getPath($mobileUi, 'public');

            $assetsPath = $this->getPath($mobileUi, 'assets');

            $mobileUiPath = public_path('ui');
            if (! $this->files->isDirectory($mobileUiPath)) {
                $this->files->makeDirectory($mobileUiPath, 0755, true);
            } elseif (! $this->files->isWritable($mobileUiPath)) {
                return [
                    'error' => true,
                    'message' => trans('packages/mobile-ui::mobile-ui.folder_is_not_writeable', ['name' => $mobileUiPath]),
                ];
            }

            $publishPath = $mobileUiPath . '/' . ($mobileUi == MobileUi::getMobileUiName() ? MobileUi::getPublicMobileUiName() : $mobileUi);

            if (! $this->files->isDirectory($publishPath)) {
                $this->files->makeDirectory($publishPath, 0755, true);
            }

            $this->files->copyDirectory($resourcePath, $publishPath);

            $iconSource = $assetsPath . '/icon/icon.png';

            if ($this->files->exists($iconSource)) {

                $this->files->copy($iconSource, public_path('icon.png'));

            } else {

                return [
                    'error' => true,
                    'message' => trans('packages/mobile-ui::mobile-ui.icon_files_not_found'),
                ];
            }

            $splashScreens = ['splash.png', 'splash-dark.png'];

            foreach ($splashScreens as $splashFile) {

                $splashSource = $assetsPath . '/splash/' . $splashFile;

                if ($this->files->exists($splashSource)) {

                    $this->files->copy($splashSource, public_path($splashFile));

                } else {

                    return [
                        'error' => true,
                        'message' => trans('packages/mobile-ui::mobile-ui.splash_files_not_found'),
                    ];
                }
            }

        }

        if (! count($mobileUis)) {
            return [
                'error' => false,
                'message' => trans('package/mobile-ui::mobile-ui.mobile-ui_no_existing'),
            ];
        }

        return [
            'error' => false,
            'message' => trans('package/mobile-ui::mobile-ui.published_assets_success', ['mobileUis' => implode(', ', $mobileUis)]),
        ];
    }

    public function remove(string $mobileUi): array
    {
        $validate = $this->validate($mobileUi);

        if ($validate['error']) {
            return $validate;
        }

        if (MobileUi::getMobileUiName() === $mobileUi) {
            return [
                'error' => true,
                'message' => trans('package/mobile-ui::mobile-ui.cannot_remove_mobile_ui', ['name' => $mobileUi]),
            ];
        }

        if (MobileUi::getInheritMobileUi() === $mobileUi) {
            return [
                'error' => true,
                'message' => trans('package/mobile-ui::mobile-ui.cannot_remove_inherit_mobile_ui', ['name' => $mobileUi]),
            ];
        }

        $this->removeAssets($mobileUi);

        $this->files->deleteDirectory($this->getPath($mobileUi));

        event(new MobileUiRemoveEvent($mobileUi));

        return [
            'error' => false,
            'message' => trans('package/mobile-ui::mobile-ui.mobile_ui_deleted', ['name' => $mobileUi]),
        ];
    }

    public function removeAssets(string $mobileUi): array
    {
        $validate = $this->validate($mobileUi);

        if ($validate['error']) {
            return $validate;
        }

        $this->files->deleteDirectory(public_path('ui/' . $mobileUi));

        return [
            'error' => false,
            'message' => trans('package/mobile-ui::mobile-ui.removed_assets', ['name' => $mobileUi]),
        ];
    }
}
