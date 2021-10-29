<?php

namespace Nagi\LaravelWopi\Contracts\Concerns;

interface Renameable
{
    /**
     * Rename the document.
     */
    public function rename(string $newName): void;

    /**
     * Indicates the user has permission to rename the current file.
     *
     * @default-value false
     */
    public function canUserRename(): bool;

    public function supportRename(): bool;
}
