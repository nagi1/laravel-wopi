<?php

namespace Nagi\LaravelWopi\Contracts\Concerns;

interface HasPermission
{
    /**
     * Indicates that, for this user, the file cannot be changed.
     *
     * @default-value false
     */
    public function isReadOnly(): bool;

    /**
     * Indicates the user does't have permission to create new files on the
     * server. Setting this to true tells the WOPI client that calls to
     * PutRelativeFile will fail for this user on the current file.
     *
     * @default-value false
     */
    public function canUserWriteRelative(): bool;

    /**
     * Indicates the user has permission to rename the current file.
     *
     * @default-value false
     */
    public function canUserRename(): bool;

    /**
     * Indicates that the user has permission to alter the
     * file. Setting this to true tells the WOPI client
     * that it can call PutFile on behalf of the user.
     *
     * @default-value false
     */
    public function canUserWrite(): bool;
}
