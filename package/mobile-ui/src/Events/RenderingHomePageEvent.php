<?php

namespace Kernery\MobileUi\Events;

use Illuminate\Queue\SerializesModels;
use Kernery\Main\Events\Event;

class RenderingHomePageEvent extends Event
{
    use SerializesModels;
}
