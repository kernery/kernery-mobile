<?php

namespace Kernery\Main\Listeners;

use Exception;
use Kernery\Main\Events\BeforeEditContentEvent;
use Kernery\Main\Facades\MainHelper;

class BeforeEditContentListener
{
    public function handle(BeforeEditContentEvent $event): void
    {
        try {
            do_action(APP_ACTION_BEFORE_EDITING_CONTENT, $event->request, $event->data);
        } catch (Exception $exception) {
            MainHelper::logError($exception);
        }
    }
}
