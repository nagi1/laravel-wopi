<?php

namespace Nagi\LaravelWopi\Contracts\Concerns;

interface OverridePermissions
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
    public function userCanNotWriteRelative(): bool;
}
