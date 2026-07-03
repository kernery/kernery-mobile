<?php

namespace Kernery\MobileUi\Supports;

use Kernery\Main\Facades\MainHelper;
use Kernery\MobileUi\Facades\MobileUi;

class Template
{
    public static function registerPageTemplate(array $templates = []): void
    {
        $validTemplates = [];

        foreach ($templates as $key => $template) {
            if (in_array($key, self::getExistsTemplate())) {
                $validTemplates[$key] = $template;
            }
        }

        config([
            'package.mobile-ui.general.templates' => array_merge(
                config('package.mobile-ui.general.templates'),
                $validTemplates
            ),
        ]);
    }

    protected static function getExistsTemplate(): array
    {
        $mobileUis = [
            MobileUi::getMobileUiName(),
        ];

        if (MobileUi::hasInheritMobileUi()) {
            $mobileUis[] = MobileUi::getInheritMobileUi();
        }

        foreach ($mobileUis as $mobileUi) {
            $files = MainHelper::scanFolder(ui_path($mobileUi . DIRECTORY_SEPARATOR . config('package.mobile-ui.general.containerDir.layout')));

            foreach ($files as $key => $file) {
                $files[$key] = str_replace('.blade.php', '', $file);
            }
        }

        return $files;
    }

    public static function getPageTemplates(): array
    {
        return (array) config('package.mobile-ui.general.templates', []);
    }
}
