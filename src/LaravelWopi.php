<?php

namespace Nagi\LaravelWopi;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Log;
use Nagi\LaravelWopi\Contracts\AbstractDocumentManager;
use Nagi\LaravelWopi\Contracts\Concerns\OverridePermissions;
use Nagi\LaravelWopi\Contracts\WopiInterface;
use Throwable;

class LaravelWopi implements WopiInterface
{
    public function checkFileInfo(string $fileId, string $accessToken, Request $request)
    {
        /** @var AbstractDocumentManager */
        $documentManager = app(AbstractDocumentManager::class);

        $document = $documentManager::find($fileId);

        return response()->json($document->getResponseProprties());
    }

    public function getFile(string $fileId, string $accessToken, Request $request)
    {
        /** @var AbstractDocumentManager */
        $documentManager = app(AbstractDocumentManager::class);

        $document = $documentManager::find($fileId);

        // The response body must be the full binary contents
        return response()->stream(function () use ($document) {
            echo $document->content();
        }, 200, [
            WopiInterface::HEADER_ITEM_VERSION => $document->version(),
            'Content-Type' => 'application/octet-stream',
            'Content-Length' => $document->size(),
            'Content-Disposition' => sprintf('attachment; filename=%s', $document->basename()),
        ]);
    }

    public function putFile(string $fileId, string $accessToken, Request $request)
    {
        /** @var AbstractDocumentManager */
        $documentManager = app(AbstractDocumentManager::class);

        $document = $documentManager::find($fileId);

        $version = $document->version();

        if (! $document->isLocked()) {
            if ($document->size() !== 0) {
                Log::error('LaravelWopi/putFile: Not locked!');

                return response('', 409, [WopiInterface::HEADER_ITEM_VERSION => $version]);
            }
        }

        $lockHeader = $request->header(WopiInterface::HEADER_LOCK);

        // If the file is currently locked and the X-WOPI-Lock value does not
        // match the lock currently on the file the host must return a 409
        // response and include an X-WOPI-Lock response header with the
        // value of the current lock on the file.
        if ($document->isLocked()) {
            $currentLock = $document->getLock();

            if ($lockHeader !== $currentLock) {
                Log::error("LaravelWopi/putFile: Lock mismatch! existing: '{$currentLock}', requested: '{$lockHeader}'");

                return response('lock mismatch', 409, [
                    WopiInterface::HEADER_LOCK => $currentLock,
                    WopiInterface::HEADER_ITEM_VERSION => $version,
                ]);
            }
        }

        $editorHeader = (string) $request->header(WopiInterface::HEADER_EDITORS);
        $editorsIds = explode(',', $editorHeader);

        // In the case where the file is unlocked, the host
        // must set X-WOPI-Lock to the empty string.
        $document->put((string) $request->getContent(), $editorsIds);
        $newVersion = $document->version();

        return response('', 200, [
            WopiInterface::HEADER_LOCK => $lockHeader,
            WopiInterface::HEADER_ITEM_VERSION => $newVersion,
        ]);
    }

    public function lock(string $fileId, string $accessToken, Request $request)
    {
        /** @var AbstractDocumentManager */
        $documentManager = app(AbstractDocumentManager::class);

        $document = $documentManager::find($fileId);

        $version = $document->version();
        $lockHeader = $request->header(WopiInterface::HEADER_LOCK);

        // If the file is currently locked and the X-WOPI-OldLock value does not
        // not match the lock currently on the file, or if the file is unlocked,
        // the host must return a 409 response include an X-WOPI-Lock response.
        if ($request->hasHeader(WopiInterface::HEADER_OLD_LOCK) && $document->isLocked()) {
            // Will be present in unlockAndRelock operation which
            // represents the current expected lock id
            $oldLockHeader = $request->header(WopiInterface::HEADER_OLD_LOCK);
            $currentLock = $document->getLock();

            if ($oldLockHeader !== $currentLock) {
                Log::error("LaravelWopi/lock: Lock mismatch! existing: '{$currentLock}', requested: '{$lockHeader}'");

                return response('', 409, [
                    WopiInterface::HEADER_LOCK => $currentLock,
                    WopiInterface::HEADER_ITEM_VERSION => $version,
                ]);
            }
        }

        // If the file is currently locked and the X-WOPI-Lock value matches
        // the lock on the file, a host should treat the request as if it
        // a RefreshLock request. then the host should refresh the lock.
        elseif ($document->isLocked()) {
            $currentLock = $document->getLock();

            if ($currentLock === $lockHeader) {
                return $this->refreshLock($fileId, $accessToken, $request);
            }

            Log::error('LaravelWopi/lock: Already locked!');

            return response('', 409, [
                WopiInterface::HEADER_LOCK => $currentLock,
                WopiInterface::HEADER_ITEM_VERSION => $version,
            ]);
        }

        $document->lock($lockHeader);

        return response('', 200, [
            WopiInterface::HEADER_ITEM_VERSION => $version,
        ]);
    }

    public function unlock(string $fileId, string $accessToken, Request $request)
    {
        /** @var AbstractDocumentManager */
        $documentManager = app(AbstractDocumentManager::class);

        $document = $documentManager::find($fileId);

        $version = $document->version();
        $lockHeader = $request->header(WopiInterface::HEADER_LOCK);

        // check if the file is locked
        if (! $document->isLocked()) {
            Log::error('LaravelWopi/unlock: Already unlocked!');

            return response('lock mismatch', 409, [
                WopiInterface::HEADER_LOCK => '',
            ]);
        }

        $currentLock = $document->getLock();

        // compare locks
        if ($currentLock !== $lockHeader) {
            Log::error("LaravelWopi/unlock: Lock mismatch! existing: '{$currentLock}', requested: '{$lockHeader}'");

            return response('', 409, [
                WopiInterface::HEADER_LOCK => $currentLock,
            ]);
        }

        // Release the lock
        $document->deleteLock();

        return response('', 200, [
            WopiInterface::HEADER_LOCK => '',
            WopiInterface::HEADER_ITEM_VERSION => $version,
        ]);
    }

    public function getLock(string $fileId, string $accessToken, Request $request)
    {
        /** @var AbstractDocumentManager */
        $documentManager = app(AbstractDocumentManager::class);

        $document = $documentManager::find($fileId);

        if ($document->isLocked()) {
            return response('', 200, [
                WopiInterface::HEADER_LOCK =>  $document->getLock(),
            ]);
        }

        return response('', 200, [
            WopiInterface::HEADER_LOCK =>  '',
        ]);
    }

    public function refreshLock(string $fileId, string $accessToken, Request $request)
    {
        $this->unlock($fileId, $accessToken, $request);

        return $this->lock($fileId, $accessToken, $request);
    }

    public function unlockAndRelock(string $fileId, string $accessToken, Request $request)
    {
        return $this->refreshLock($fileId, $accessToken, $request);
    }

    public function deleteFile(string $fileId, string $accessToken, Request $request)
    {
        $documentManager = app(AbstractDocumentManager::class);

        /** @var AbstractDocumentManager */
        $document = $documentManager::find($fileId);

        if ($document->isLocked()) {
            Log::error('LaravelWopi/deleteFile: File is locked!');

            return response('', 409, [
                WopiInterface::HEADER_LOCK =>  $document->getLock(),
            ]);
        }

        $document->delete();

        return response('', 200);
    }

    public function renameFile(string $fileId, string $accessToken, Request $request)
    {
        $requestedName = $request->hasHeader(WopiInterface::HEADER_REQUESTED_NAME) ?
        mb_convert_encoding($request->header(WopiInterface::HEADER_REQUESTED_NAME), 'UTF-8', 'UTF-7') :
        false;

        if (! $requestedName) {
            return response('', 400);
        }

        /** @var AbstractDocumentManager */
        $documentManager = app(AbstractDocumentManager::class);

        $document = $documentManager::find($fileId);

        if ($document->isLocked()) {
            $currentLock = $document->getLock();
            $lockHeader = $request->header(WopiInterface::HEADER_LOCK);

            if ($lockHeader !== $currentLock) {
                Log::error("LaravelWopi/renameFile: Lock mismatch! existing: '{$currentLock}', requested: '{$lockHeader}'");

                return response('lock mismatch', 409, [
                    WopiInterface::HEADER_LOCK => $currentLock,
                ]);
            }
        }

        // If the host cannot rename the file because the name requested
        // is invalid or conflicts with existing file, the host should
        // try to generate different name based on the requested name.
        try {
            $document->rename($requestedName);
        } catch (Throwable $e) {
            throw $e;
            $requestedName = sprintf('%s-%s', $requestedName, 'Renamed');

            // If the host cannot generate a different name, it should
            // return HTTP status code 400 Bad Request. The response
            // must include an X-WOPI-InvalidFileNameError header
            // that describes why the file name was invalid.
            try {
                $document->rename($requestedName);
            } catch (Throwable $e) {
                throw $e;

                return response('', 400, [WopiInterface::HEADER_INVALID_FILE_NAME_ERROR => (string) $e->getMessage()]);
            }
        }

        return response()->json([
            'Name' =>  $requestedName,
        ]);
    }

    public function putRelativeFile(string $fileId, string $accessToken, Request $request)
    {
        /** @var AbstractDocumentManager */
        $documentManager = app(AbstractDocumentManager::class);

        // If documentmanager supportsUpdate in CheckFileInfo, it expected to
        // implement the PutRelativeFile operation. However, if chose to not
        // implement this operation even though SupportsUpdate is true...
        if ($documentManager instanceof OverridePermissions) {
            if ($documentManager->userCanNotWriteRelative()) {
                return response('Not Implemented', 501);
            }
        }

        // This operation has two distinct modes: specific and suggested. The
        // difference betweenthem is whether the client expects the host to
        // use the file name provided exactly (specific mode) or if the
        // host can adjust the file name (suggested mode).
        $suggestedTargetHeader = $request->hasHeader(WopiInterface::HEADER_SUGGESTED_TARGET)
                ? mb_convert_encoding($request->header(WopiInterface::HEADER_SUGGESTED_TARGET), 'UTF-8', 'UTF-7')
                : null;

        $relativeTargetHeader = $request->hasHeader(WopiInterface::HEADER_RELATIVE_TARGET)
                ? mb_convert_encoding($request->header(WopiInterface::HEADER_RELATIVE_TARGET), 'UTF-8', 'UTF-7')
                : null;

        // Specifies whether the host must overwrite the file name if it exists.
        // The default value is false. If X-WOPI-OverwriteRelativeTarget is
        // not explicitly included on the request, hosts must behave as
        // though its value is false.
        $overwriteRelativeTargetHeader = $this->nullableStrToBool($request->header(WopiInterface::HEADER_OVERWRITE_RELATIVE_TARGET));

        $size = $request->header(WopiInterface::HEADER_SIZE);

        // check if both headers are present
        if (! empty($suggestedTargetHeader) && ! empty($relativeTargetHeader)) {
            return response('', 400);
        }

        if (! empty($suggestedTargetHeader)) {
            $document = $documentManager::find($fileId);

            // default to that $suggestedTarget is full file name
            $suggestedTarget = $suggestedTargetHeader;

            if (Str::startsWith($suggestedTargetHeader, '.')) {
                $filename = pathinfo($document->basename(), PATHINFO_FILENAME);

                // $suggestedTargetHeader in this case is the extension
                $suggestedTarget = sprintf('%s%s', $filename, $suggestedTargetHeader);
            }

            $target = $suggestedTarget;
        }

        if (! empty($relativeTargetHeader)) {
            try {
                $relativeDocument = $documentManager::findByName($relativeTargetHeader);
            } catch (Throwable $e) {
                $relativeDocument = false;
            }

            if ($relativeDocument) {
                // If the file with the specified name already exists, the
                // the host must respond with a 409 Conflict, unless the
                // X-WOPI-OverwriteRelativeTarget header is set to true.
                if (! $overwriteRelativeTargetHeader) {
                    $extension = pathinfo($relativeDocument->basename(), PATHINFO_EXTENSION);

                    // When responding with a 409 Conflict for this reason,
                    // the host may include an X-WOPI-ValidRelativeTarget
                    // specifying a file name that is valid.
                    Log::error('LaravelWopi/putRelativeFile: Overwriting not allowed!');

                    return response()->json([], 409, [
                        WopiInterface::HEADER_VALID_RELATIVE_TARGET => sprintf('%s.%s', uniqid(), $extension),
                    ]);
                }

                // If the X-WOPI-OverwriteRelativeTarget header is set to true
                // and a file with the specified name already exists and is
                // locked the host must respond with a 409 Conflict and
                // include an X-WOPI-Lock response header with lockid.
                if ($relativeDocument->isLocked()) {
                    Log::error('LaravelWopi/putRelativeFile: Existing file is locked!');

                    return response()->json([], 409, [
                        WopiInterface::HEADER_LOCK => $relativeDocument->getLock(),
                    ]);
                }
            }

            $target = $relativeTargetHeader;
        }

        $pathInfo = pathinfo($target);

        // Popular OS set maximum of 255 characters including the full path
        if (strlen($target) > 150) {
            $ext = $pathInfo['extension'];
            $acceptableFilenameLength = 150 - strlen($ext);

            $target = substr($pathInfo['filename'], 0, $acceptableFilenameLength).".{$ext}";
        }

        /** @var AbstractDocumentManager */
        $newDocument = $documentManager::create([
            'basename' => $target,
            'name' => $pathInfo['filename'],
            'extension' => $pathInfo['extension'],
            'content' => (string) $request->getContent(),
            'size' => $size,
        ]);

        $generateUrl = fn ($fileId) => sprintf('%s?access_token=%s', route('wopi.checkFileInfo', ['file_id' => $fileId]), $accessToken);

        $properties = [
            'Name' => $newDocument->basename(),
            'Url' => (string) $generateUrl($newDocument->id()),
            // Todo support this features correctly
            'HostEditUrl' => (string) $generateUrl($newDocument->id()),
            'HostViewUrl' => (string) $generateUrl($newDocument->id()),
        ];

        return response()->json($properties);
    }

    public function putUserInfo(string $fileId, string $accessToken, Request $request)
    {
        /** @var AbstractDocumentManager */
        $documentManager = app(AbstractDocumentManager::class);

        $documentManager::putUserInfo((string) $request->getContent(), $fileId, $accessToken);

        return response('', 200);
    }

    public function enumerateAncestors(string $fileId, string $accessToken, Request $request)
    {
        // Not implemented
        return response('', 501);
    }

    private function nullableStrToBool(?string $str): bool
    {
        if (is_null($str)) {
            return false;
        }

        $str = strtolower($str);

        if ($str === 'true') {
            return true;
        }

        return false;
    }
}
