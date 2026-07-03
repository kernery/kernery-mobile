<?php

namespace Kernery\Main\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Kernery\Main\Contracts\MainModel;

class BeforeUpdateContentEvent extends Event
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public Request $request, public bool | MainModel | null $data) {}
}
