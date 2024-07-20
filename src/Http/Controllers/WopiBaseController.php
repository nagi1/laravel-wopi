<?php

namespace Nagi\LaravelWopi\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class WopiBaseController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function hasAccessToken(Request $request): bool
    {
        return ! empty($request->query('access_token'));
    }

    /**
     * @param Request $request
     * @param string $header
     * @param string|int $value
     *
     * @return bool
     */
    public function isHeaderSetTo(Request $request, string $header, $value): bool
    {
        return $request->hasHeader($header) && $request->header($header) === $value;
    }
}
