<?php

namespace Kernery\Main\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use Kernery\Main\Http\Controllers\Concerns\HasHttpResponse;
use Kernery\Main\Http\Controllers\Concerns\HasPageTitle;

class MainController extends Controller
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use HasHttpResponse;
    use HasPageTitle;
    use ValidatesRequests;
}
