<?php

namespace Nagi\LaravelWopi;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Nagi\LaravelWopi\Contracts\AbstractDocumentManager;
use Nagi\LaravelWopi\Contracts\WopiInterface;
use Throwable;

class LaravelWopi implements WopiInterface
{
    public function checkFileInfo(string $fileId, string $accessToken, Request $request)
    {
        /** @var AbstractDocumentManager */
        $documentManager = app(AbstractDocumentManager::class);

        // Todo implement some sort of caching mechanism
        $document = $documentManager::find($fileId);

        return response()->json($document->getResponseProprties());
    }

    public function getFile(string $fileId, string $accessToken, Request $request)
    {/** @var AbstractDocumentManager */
        $documentManager = app(AbstractDocumentManager::class);

        $document = $documentManager::find($fileId);

        // The response body must be the full binary contents
        return response()->stream(function () use ($document) {
            echo $document->content();
        }, 200, [
            WopiInterface::HEADER_ITEM_VERSION => sprintf('v%s', $document->version()),
            'Content-Type' => 'application/octet-stream',
            'Content-Length' => $document->size(),
            'Content-Disposition' => sprintf('attachment; filename=%s', $document->basename()),
        ]);
    }

    public function putFile(string $fileId, string $accessToken, Request $request)
    {/** @var AbstractDocumentManager */
        $documentManager = app(AbstractDocumentManager::class);

        /** @var AbstractDocumentManager */
        $document = $documentManager::find($fileId);

        $version = $document->version();
        $lockHeader = $request->header(WopiInterface::HEADER_LOCK);
        $editorsIds = $request->header(WopiInterface::HEADER_EDITORS);

        // Todo this for microsoft office 365
        // see https://docs.microsoft.com/en-us/microsoft-365/cloud-storage-partner-program/rest/files/putfile
        // if (! $document->hasLock()) {
        //     if ($document->size() !== 0) {
        //         return response('', 409, [WopiInterface::HEADER_ITEM_VERSION=> $version]);
        //     }
        // }

        // If the file is currently locked and the X-WOPI-Lock value does not
        // match the lock currently on the file the host must return a 409
        // response and include an X-WOPI-Lock response header with the
        // value of the current lock on the file.
        if ($document->isLocked()) {
            $currentLock = $document->getLock();

            if ($lockHeader !== $currentLock) {
                return response('lock mismatch', 409, [
                    WopiInterface::HEADER_LOCK => $currentLock,
                    WopiInterface::HEADER_ITEM_VERSION => $version,
                ]);
            }
        }

        // In the case where the file is unlocked, the host
        // must set X-WOPI-Lock to the empty string.
        $document->put($request->getContent(), $editorsIds);
        $newVersion = $document->version();

        return response('', 200, [
            WopiInterface::HEADER_LOCK => $lockHeader,
            WopiInterface::HEADER_ITEM_VERSION => $newVersion,
        ]);
    }

    public function lock(string $fileId, string $accessToken, Request $request)
    {/** @var AbstractDocumentManager */
        $documentManager = app(AbstractDocumentManager::class);

        /** @var AbstractDocumentManager */
        $document = $documentManager::find($fileId);

        $version = $document->version();
        $lockHeader = $request->header(WopiInterface::HEADER_LOCK);

        // If the file is currently locked and the X-WOPI-Lock value matches
        // the lock on the file, a host should treat the request as if it
        // a RefreshLock request. then the host should refresh the lock.
        if ($document->isLocked()) {
            $currentLock = $document->getLock();
            if ($currentLock == $lockHeader) {
                return $this->refreshLock($fileId, $accessToken, $request);
            }

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
    {/** @var AbstractDocumentManager */
        $documentManager = app(AbstractDocumentManager::class);

        /** @var AbstractDocumentManager */
        $document = $documentManager::find($fileId);

        $version = $document->version();
        $lockHeader = $request->header(WopiInterface::HEADER_LOCK);

        // Will be present in unlockAndRelock operation
        $oldLockHeader = $request->header(WopiInterface::HEADER_OLD_LOCK);

        // check if the file is locked
        if (! $document->isLocked()) {
            return response('lock mismatch', 409, [
                WopiInterface::HEADER_LOCK => $lockHeader,
            ]);
        }

        $currentLock = $document->getLock();

        if (! is_null($oldLockHeader)) {
            $currentLock = $oldLockHeader;
        }

        // compare locks
        if ($currentLock !== $lockHeader) {
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
    {/** @var AbstractDocumentManager */
        $documentManager = app(AbstractDocumentManager::class);

        /** @var AbstractDocumentManager */
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
    {/** @var AbstractDocumentManager */
        $documentManager = app(AbstractDocumentManager::class);

        /** @var AbstractDocumentManager */
        $document = $documentManager::find($fileId);

        if ($document->isLocked()) {
            return response('', 409, [
                WopiInterface::HEADER_LOCK =>  $document->getLock(),
            ]);
        }

        $document->delete();

        return response('', 200);
    }

    public function renameFile(string $fileId, string $accessToken, string $requestedName, Request $request)
    {/** @var AbstractDocumentManager */
        $documentManager = app(AbstractDocumentManager::class);

        /** @var AbstractDocumentManager */
        $document = $documentManager::find($fileId);

        if ($document->isLocked()) {
            $currentLock = $document->getLock();
            $lockHeader = $request->header(WopiInterface::HEADER_LOCK);

            if ($lockHeader !== $currentLock) {
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
            $requestedName = sprintf('%s-%s', $requestedName, 'renamed');

            // If the host cannot generate a different name, it should
            // return HTTP status code 400 Bad Request. The response
            // must include an X-WOPI-InvalidFileNameError header
            // that describes why the file name was invalid.
            try {
                $document->rename($requestedName);
            } catch (Throwable $e) {
                return response('', 400, [WopiInterface::HEADER_INVALID_FILE_NAME_ERROR => (string) $e->getMessage()]);
            }
        }

        return response()->json([
            'Name' =>  $requestedName,
        ]);
    }

    public function putRelativeFile(string $fileId, string $accessToken, Request $request)
    {/** @var AbstractDocumentManager */
        $documentManager = app(AbstractDocumentManager::class);

        /** @var AbstractDocumentManager */
        $document = $documentManager::find($fileId);

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
        $overwriteRelativeTargetHeader = $request->hasHeader(WopiInterface::HEADER_OVERWRITE_RELATIVE_TARGET)
        ? (strtolower($request->header(WopiInterface::HEADER_OVERWRITE_RELATIVE_TARGET) === 'false') ? false : true)
        : false;

        // both headers are present
        if (! empty($suggestedTargetHeader) && ! empty($relativeTargetHeader)) {
            return response('', 400);
        }

        if (! empty($suggestedTargetHeader)) {
            if (Str::startsWith($suggestedTargetHeader, '.')) {
                $filename = pathinfo($document->basename(), PATHINFO_FILENAME);

                $suggestedTarget = sprintf('%s%s', $filename, $suggestedTargetHeader);
            }

            $target = $suggestedTarget;
        }

        if (! empty($relativeTargetHeader)) {
            $relativeDocument = $documentManager::findByName($relativeTargetHeader);

            if ($relativeDocument) {

                // If the file with the specified name already exists, the
                // the host must respond with a 409 Conflict, unless the
                // X-WOPI-OverwriteRelativeTarget header is set to true.
                if (! $overwriteRelativeTargetHeader) {
                    $extension = pathinfo($relativeDocument->basename(), PATHINFO_EXTENSION);

                    // When responding with a 409 Conflict for this reason,
                    // the host may include an X-WOPI-ValidRelativeTarget
                    // specifying a file name that is valid.
                    return response()->json([], 409, [
                        WopiInterface::HEADER_VALID_RELATIVE_TARGET => sprintf('%s.%s', uniqid(), $extension),
                    ]);
                }

                // If the X-WOPI-OverwriteRelativeTarget header is set to true
                // and a file with the specified name already exists and is
                // locked the host must respond with a 409 Conflict and
                // include an X-WOPI-Lock response header with lockid.
                if ($relativeDocument->isLocked()) {
                    return response()->json([], 409, [
                        WopiInterface::HEADER_LOCK => $relativeDocument->getLock(),
                    ]);
                }
            }

            $target = $relativeTargetHeader;
        }

        /** @var AbstractDocumentManager */
        $newDocument = $documentManager::create([
            'name' => $target,
            'content' => (string) $request->getContent(),
            'size' => $request->header(WopiInterface::HEADER_SIZE),
        ]);

        $url = route('wopi.checkFileInfo', ['file_id' => $newDocument->id()]);
        $url = sprintf('%s?access_token=%s', $url, $accessToken);

        $properties = [
            'Name' => $newDocument->basename(),
            'Url' => (string) $url,
            'HostEditUrl' => $newDocument->id(),
            'HostViewUrl' => $newDocument->id(),
        ];

        return response()->json($properties);
    }

    public function enumerateAncestors(string $fileId, string $accessToken, Request $request)
    {
        // Not implemented
        return response('', 501);
    }
}
