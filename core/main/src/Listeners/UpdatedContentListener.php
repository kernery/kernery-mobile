<?php

namespace Kernery\Main\Listeners;

use Exception;
use Kernery\Main\Events\UpdatedContentEvent;
use Kernery\Main\Facades\MainHelper;

class UpdatedContentListener
{
    public function handle(UpdatedContentEvent $event): void
    {
        try {
            do_action(APP_ACTION_AFTER_UPDATING_CONTENT, $event->screen, $event->request, $event->data);
        } catch (Exception $exception) {
            MainHelper::logError($exception);
        }
    }
}
