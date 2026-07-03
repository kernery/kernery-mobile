<?php

namespace Kernery\MobileUi\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Routing\Router;
use Kernery\Main\Events\Event;

class MobileUiRoutingAfterEvent extends Event
{
    use SerializesModels;

    public function __construct(public Router $router) {}
}
