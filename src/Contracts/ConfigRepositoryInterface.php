<?php

namespace Nagi\LaravelWopi\Contracts;

interface ConfigRepositoryInterface
{
    public function getWopiClientUrl(): string;

    public function getDefaultUiLang(): string;

    public function getDiscoveryXMLConfigFile(): ?string;

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
}
