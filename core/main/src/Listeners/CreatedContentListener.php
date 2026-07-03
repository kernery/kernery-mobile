<?php

namespace Kernery\Main\Listeners;

use Exception;
use Kernery\Main\Events\CreatedContentEvent;
use Kernery\Main\Facades\MainHelper;

class CreatedContentListener
{
    public function handle(CreatedContentEvent $event): void
    {
        try {
            do_action(APP_ACTION_AFTER_CREATING_CONTENT, $event->screen, $event->request, $event->data);
        } catch (Exception $exception) {
            MainHelper::logError($exception);
        }
    }
}
