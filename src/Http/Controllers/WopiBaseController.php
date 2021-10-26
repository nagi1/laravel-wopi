<?php

namespace Nagi\LaravelWopi\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;

class WopiBaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}
