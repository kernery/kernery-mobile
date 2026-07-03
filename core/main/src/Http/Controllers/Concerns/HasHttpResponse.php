<?php

namespace Kernery\Main\Http\Controllers\Concerns;

use Kernery\Main\Http\Responses\MainHttpResponse;

trait HasHttpResponse
{
    public function httpResponse(): MainHttpResponse
    {
        return MainHttpResponse::make();
    }
}
