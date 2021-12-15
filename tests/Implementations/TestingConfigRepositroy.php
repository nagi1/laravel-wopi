<?php

namespace Nagi\LaravelWopi\Tests\Implementations;

use Nagi\LaravelWopi\Contracts\ConfigRepositoryInterface;

class TestingConfigRepositroy implements ConfigRepositoryInterface
{
    public function getWopiClientUrl(): string
    {
        return 'http://localhost:9980';
    }

    public function getDefaultUiLang(): string
    {
    }

    public function supportGetLocks(): bool
    {
    }

    public function supportExtendedLockLength(): bool
    {
    }

    public function supportUserInfo(): bool
    {
    }

    public function getMiddleware(): array
    {
    }

    public function getDefaultUser(): string
    {
    }

    public function supportDelete(): bool
    {
        return true;
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
