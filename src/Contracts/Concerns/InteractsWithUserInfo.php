<?php

namespace Nagi\LaravelWopi\Contracts\Concerns;

interface InteractsWithUserInfo
{
    /**
     * Store the string information about the user received from the wopi client.
     */
    public static function putUserInfo(string $userInfo, ?string $fileId, ?string $accessToken): void;

    /**
     * A string containing information about the user. WOPI clients can passed
     * to the host by using PutUserInfo operation. If the host has a UserInfo
     * string for the user, they must include it in this property.
     */
    public function getUserInfo(): string;

    /**
     * Wither to enable or disable this functionality.
     */
    public function supportUserInfo(): bool;
}
