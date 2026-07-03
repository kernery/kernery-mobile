<?php

namespace Kernery\Main\Http\Controllers;

use Kernery\Main\Facades\MainHelper;
use Kernery\Main\Supports\AppCore;

class SystemController extends MainController
{
    public function getCheckUpdate(AppCore $appCore)
    {
        $response = $this->httpResponse();

        if (! config('core.main.general.enable_app_updater')) {
            return $response;
        }

        $response
            ->setData(['has_new_version' => false]);

        $updateData = $appCore->checkUpdate();

        if ($updateData) {
            $response
                ->setData(['has_new_version' => true])
                ->setMessage(
                    sprintf(
                        'A new version (%s / released on %s) is available to update',
                        $updateData->version,
                        MainHelper::formatDate($updateData->releasedDate)
                    )
                );
        }

        return $response;
    }
}
