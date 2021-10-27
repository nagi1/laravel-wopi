<?php

namespace Nagi\LaravelWopi\Contracts\Concerns;

interface HasHash
{
    /**
     * A 256 bit SHA-2-encoded hash of the file contents, as Base64-encoded
     * string. Used for caching purposes in WOPI clients. be careful when
     * calculating hashes for huge files that might impact performance.
     *
     * @default-value not null empty string
     */
    public function sha256Hash(): string;
}
