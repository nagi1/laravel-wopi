<?php

namespace Nagi\LaravelWopi\Contracts;

use Illuminate\Http\JsonResponse;
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

    public function checkFileInfo(string $fileId, string $accessToken, Request $request): JsonResponse;
}
