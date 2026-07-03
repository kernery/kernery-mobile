<?php

namespace Kernery\Main\Events;

use Illuminate\Http\Request;
use Illuminate\Queue\SerializesModels;
use Kernery\Main\Contracts\MainModel;

class BeforeEditContentEvent extends Event
{
    use SerializesModels;

    public function __construct(public Request $request, public bool | MainModel | null $data) {}
}
