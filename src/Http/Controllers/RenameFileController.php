<?php

namespace Nagi\LaravelWopi\Http\Controllers;

use Nagi\LaravelWopi\Contracts\WopiInterface;
use Nagi\LaravelWopi\Http\Requests\WopiRequest;
use Nagi\LaravelWopi\Support\RequestHelper;

class RenameFileController extends WopiBaseController
{
    public function __invoke(WopiRequest $request, string $fileId, WopiInterface $wopiImplementation)
    {
        $requestedName = $request->hasHeader(WopiInterface::HEADER_REQUESTED_NAME) ?
        mb_convert_encoding($request->header(WopiInterface::HEADER_REQUESTED_NAME), 'UTF-8', 'UTF-7') :
        false;

        if (! $requestedName) {
            return response('', 400);
        }

        $accessToken = RequestHelper::parseAccessToken($request);

        return $wopiImplementation->renameFile($fileId, $accessToken, $requestedName, $request);
    }
}
