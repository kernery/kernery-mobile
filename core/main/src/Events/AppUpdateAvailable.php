<?php

namespace Kernery\Main\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Kernery\Main\Supports\ValueObjects\AppProject;

class AppUpdateAvailable
{
    use Dispatchable;

    public function __construct(public AppProject $coreApplication) {}
}
