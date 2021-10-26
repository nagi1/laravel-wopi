<?php

namespace Nagi\LaravelWopi\Tests\Implementations;

use Nagi\LaravelWopi\Contracts\ConfigRepositoryInterface;

class TestingConfigRepositroy implements ConfigRepositoryInterface
{
    public function getEnableProofValidation(): bool
    {
        return true;
    }

    public function getWopiServerUrl(): string
    {
        return 'http://localhost:9980';
    }

    public function getDiscoveryXMLConfigFile(): ?string
    {
        return file_get_contents(__DIR__.'/Unit/Services/discovery.xml');
    }

    public function getAccessTokenTTL(): int
    {
        return 0;
    }
}
