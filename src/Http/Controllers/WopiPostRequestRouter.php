<?php

namespace Nagi\LaravelWopi\Http\Controllers;

use Nagi\LaravelWopi\Contracts\WopiInterface;
use Nagi\LaravelWopi\Http\Requests\WopiRequest;

class WopiPostRequestRouter extends WopiBaseController
{
    public function __invoke(WopiRequest $request, string $fileId, WopiInterface $wopiImplementation)
    {
        // Microsoft used the headers as a second routing layer
        // and even with this second layer the LOCK header is
        // present in 2 request that we also need to handle.
        switch ($request->header(WopiInterface::HEADER_OVERRIDE)) {
            case 'LOCK':
                return $request->hasHeader(WopiInterface::HEADER_OLD_LOCK)
                    ? app(UnlockAndRelockController::class)($request, $fileId, $wopiImplementation)
                    : app(LockController::class)($request, $fileId, $wopiImplementation);
                break;
            case 'GET_LOCK': return app(GetLockController::class)($request, $fileId, $wopiImplementation);
                break;
            case 'REFRESH_LOCK': return app(RefreshLockController::class)($request, $fileId, $wopiImplementation);
                break;
            case 'UNLOCK': return app(UnlockController::class)($request, $fileId, $wopiImplementation);
                break;
            case 'PUT_RELATIVE': return app(PutRelativeFileController::class)($request, $fileId, $wopiImplementation);
                break;
            case 'RENAME_FILE': return app(RenameFileController::class)($request, $fileId, $wopiImplementation);
                break;
            case 'DELETE': return app(DeleteFileController::class)($request, $fileId, $wopiImplementation);
                break;
            case 'PUT_USER_INFO': return app(PutUserInfoController::class)($request, $fileId, $wopiImplementation);
                break;

                // Anything else is a bad request
            default: return response('', 400);
        }
    }
}
