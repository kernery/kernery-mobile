<?php

namespace Kernery\MobileUi\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Kernery\Main\Events\AppUpdatePublished;
use Kernery\Main\Events\SeederPrepared;
use Kernery\MobileUi\Listeners\PublishMobileUiAssets;
use Kernery\MobileUi\Listeners\SetDefaultMobileUi;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        AppUpdatePublished::class => [
            PublishMobileUiAssets::class,
        ],
        SeederPrepared::class => [
            SetDefaultMobileUi::class,
        ],
    ];
}
