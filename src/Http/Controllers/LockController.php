<?php

namespace Nagi\LaravelWopi\Http\Controllers;

use Nagi\LaravelWopi\Contracts\WopiInterface;
use Nagi\LaravelWopi\Http\Requests\WopiRequest;
use Nagi\LaravelWopi\Support\RequestHelper;

class LockController extends WopiBaseController
{
    public function __invoke(WopiRequest $request, string $fileId, WopiInterface $wopiImplementation)
    {
        $requiredHeadersArePresent =
                    $this->hasAccessToken($request)
                    && $request->hasHeader(WopiInterface::HEADER_LOCK)
                    && $this->isHeaderSetTo($request, WopiInterface::HEADER_OVERRIDE, 'LOCK');

        abort_unless($requiredHeadersArePresent, 400);

        $accessToken = RequestHelper::parseAccessToken($request);

        return $wopiImplementation->lock($fileId, $accessToken, $request);
    }
}
