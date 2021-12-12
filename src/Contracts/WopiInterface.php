<?php

namespace Nagi\LaravelWopi\Contracts;

use Illuminate\Http\Request;

interface WopiInterface
{
    public const HEADER_EDITORS = 'X-WOPI-Editors';

    public const HEADER_ITEM_VERSION = 'X-WOPI-ItemVersion';

    public const HEADER_LOCK = 'X-WOPI-Lock';

    public const HEADER_OLD_LOCK = 'X-WOPI-OldLock';

    public const HEADER_OVERRIDE = 'X-WOPI-Override';

    public const HEADER_OVERWRITE_RELATIVE_TARGET = 'X-WOPI-OverwriteRelativeTarget';

    public const HEADER_PROOF = 'X-WOPI-Proof';

    public const HEADER_PROOF_OLD = 'X-WOPI-ProofOld';

    public const HEADER_RELATIVE_TARGET = 'X-WOPI-RelativeTarget';

    public const HEADER_REQUESTED_NAME = 'X-WOPI-RequestedName';

    public const HEADER_SIZE = 'X-WOPI-Size';

    public const HEADER_SUGGESTED_TARGET = 'X-WOPI-SuggestedTarget';

    public const HEADER_TIMESTAMP = 'X-WOPI-Timestamp';

    public const HEADER_URL_TYPE = 'X-WOPI-UrlType';

    public const HEADER_VALID_RELATIVE_TARGET = 'X-WOPI-ValidRelativeTarget';

    public const HEADER_INVALID_FILE_NAME_ERROR = 'X-WOPI-InvalidFileNameError';

    /**
     * One of the most important WOPI operations. must be implemented for
     * all WOPI actions. it returns information about a file, a user’s
     * permissions on that file and influence the appearance of ui.
     *
     * @param string $accessToken raw access token
     *
     * @return \Illuminate\Http\JsonResponse must return json response.
     */
    public function checkFileInfo(string $fileId, string $accessToken, Request $request);

    /**
     * Retrieve the binary content for the file. including
     * X-WOPI-ItemVersion header indicating the version
     * of the file. Its value should be the same
     * as Version value in CheckFileInfo.
     *
     * @param string $accessToken raw access token
     *
     * @return \Symfony\Component\HttpFoundation\StreamedResponse must return binary response.
     */
    public function getFile(string $fileId, string $accessToken, Request $request);

    /**
     * Updates a file’s binary contents.
     *
     * @param string $accessToken raw access token
     *
     * @return \Illuminate\Http\Response
     */
    public function putFile(string $fileId, string $accessToken, Request $request);

    /**
     * Locks a file for editing by the WOPI client that requested the lock. To
     * support editing files, WOPI clients require that the WOPI host support
     * locking files. the file should not be writable by other applications.
     *
     * @param string $accessToken raw access token
     *
     * @return \Illuminate\Http\Response
     */
    public function lock(string $fileId, string $accessToken, Request $request);

    /**
     * Releases the lock on a file.
     *
     * @param string $accessToken raw access token
     *
     * @return \Illuminate\Http\Response
     */
    public function unlock(string $fileId, string $accessToken, Request $request);

    /**
     * Retrieves a lock on a file. It does not create a new lock. returns the current
     * lock value to the X-WOPI-Lock response header. If the file is currently not
     * locked, the host must include X-WOPI-Lock response with an empty string.
     *
     * @param string $accessToken raw access token
     *
     * @return \Illuminate\Http\Response
     */
    public function getLock(string $fileId, string $accessToken, Request $request);

    /**
     * Refreshes the lock on a file by resetting its automatic expiration timer
     * to 30 minutes. The refreshed lock must expire automatically after 30
     * minutes unless it is modified by a subsequent WOPI operation.
     *
     * @param string $accessToken raw access token
     *
     * @return \Illuminate\Http\Response
     */
    public function refreshLock(string $fileId, string $accessToken, Request $request);

    /**
     * Alias for refreshLock but with diffrent header X-WOPI-OldLock.
     *
     * @param string $accessToken raw access token
     *
     * @return \Illuminate\Http\Response
     */
    public function unlockAndRelock(string $fileId, string $accessToken, Request $request);

    /**
     * Delete the file from the host.
     *
     * @param string $accessToken raw access token
     *
     * @return \Illuminate\Http\Response
     */
    public function deleteFile(string $fileId, string $accessToken, Request $request);

    /**
     * Renames a file. It should not change file id.
     *
     * @param string $fileId
     * @param string $accessToken
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function renameFile(string $fileId, string $accessToken, Request $request);

    /**
     * Creates a new file on the host based on the
     * current file. The host must use the content
     * in the POST body to create the new file.
     *
     * @param string $fileId
     * @param string $accessToken
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function putRelativeFile(string $fileId, string $accessToken, Request $request);

    /**
     * Currently unimplemented.
     *
     * @param string $fileId
     * @param string $accessToken
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function enumerateAncestors(string $fileId, string $accessToken, Request $request);

    /**
     * Stores basic user information on the host. Hosts must store
     * the UserInfo string which is contained in the body of the
     * request. The UserInfo string should be associated with a
     * particular user, and should be passed back to the WOPI.
     *
     * @param string $fileId
     * @param string $accessToken
     * @param \Illuminate\Http\Request $request Contains body has a maximum size of 1024 ASCII characters.
     *
     * @return \Illuminate\Http\Response
     */
    public function putUserInfo(string $fileId, string $accessToken, Request $request);
}
