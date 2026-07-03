<?php

namespace Kernery\Support\Services;

use Illuminate\Http\Request;

interface ProduceServiceInterface
{
    public function execute(Request $request);
}
