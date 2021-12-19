<?php

namespace Nagi\LaravelWopi\Tests\Implementations;

use Nagi\LaravelWopi\Contracts\ConfigRepositoryInterface;

class TestingConfigRepositroy implements ConfigRepositoryInterface
{
    public function getWopiClientUrl(): string
    {
        return '';
    }

    public function getDefaultUiLang(): string
    {
        return '';
    }

    public function supportGetLocks(): bool
    {
        return '';
    }

    public function supportExtendedLockLength(): bool
    {
        return '';
    }

    public function supportUserInfo(): bool
    {
        return false;
    }

    public function getMiddleware(): array
    {
        return [];
    }

    public function getDefaultUser(): string
    {
        return '';
    }

    public function supportDelete(): bool
    {
        return false;
    }

    public function supportUpdate(): bool
    {
        return true;
    }

    public function supportRename(): bool
    {
        return true;
    }

    public function supportLocks(): bool
    {
        return true;
    }

    public function getEnableProofValidation(): bool
    {
        return true;
    }

    public function getDiscoveryXMLConfigFile(): ?string
    {
        return file_get_contents(__DIR__.'/../Unit/Services/discovery.xml');
    }

    public function getAccessTokenTTL(): int
    {
        return 0;
    }
}
