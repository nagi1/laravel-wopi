<?php

namespace Nagi\LaravelWopi\Http\Controllers;

use Nagi\LaravelWopi\Contracts\WopiInterface;
use Nagi\LaravelWopi\Http\Requests\WopiRequest;
use Nagi\LaravelWopi\Support\RequestHelper;

class RefreshLockController extends WopiBaseController
{
    public function __invoke(WopiRequest $request, string $fileId, WopiInterface $wopiImplementation)
    {
        $accessToken = RequestHelper::parseAccessToken($request);

        return $wopiImplementation->refreshLock($fileId, $accessToken, $request);
    }
}
