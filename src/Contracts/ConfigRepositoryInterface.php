<?php

namespace Nagi\LaravelWopi\Contracts;

interface ConfigRepositoryInterface
{
    public function getWopiClientUrl(): string;

    public function getWopiHostUrl(): string;

    public function getDefaultUiLang(): string;

    public function getDiscoveryXMLConfigFile(): ?string;

    /**
     * Seconds the discovery document stays cached, 0 disables caching.
     */
    public function getDiscoveryCacheTtl(): int;

    /**
     * Cache store for the discovery document, null uses the default store.
     */
    public function getDiscoveryCacheStore(): ?string;

    public function getAccessTokenTTL(): int;

    public function getEnableProofValidation(): bool;

    public function supportDelete(): bool;

    public function supportUpdate(): bool;

    public function supportRename(): bool;

    public function supportLocks(): bool;

    public function supportGetLocks(): bool;

    public function supportExtendedLockLength(): bool;

    public function supportUserInfo(): bool;

    public function getMiddleware(): array;

    public function getDefaultUser(): string;

    public function getMicrosoft365UrlPlaceholderValueMap(): array;

    public function getEnableInteractiveWopiValidation(): bool;

    public function isMicrosoft365Enabled(): bool;
}
