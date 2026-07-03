<?php

namespace Kernery\MobileUi\Events;

use Illuminate\Queue\SerializesModels;
use Kernery\Main\Events\Event;

class MobileUiRemoveEvent extends Event
{
    use SerializesModels;

    public function __construct(public string $mobileUi) {}
}
