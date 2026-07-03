<?php

namespace Kernery\Main\Listeners;

use Exception;
use Kernery\Main\Contracts\MainModel;
use Kernery\Main\Events\DeletedContentEvent;
use Kernery\Main\Facades\MainHelper;

class DeletedContentListener
{
    public function handle(DeletedContentEvent $event): void
    {
        if (! $event->data instanceof MainModel) {
            return;
        }

        try {
            do_action(APP_ACTION_AFTER_DELETING_CONTENT, $event->screen, $event->request, $event->data);
        } catch (Exception $exception) {
            MainHelper::logError($exception);
        }
    }
}
