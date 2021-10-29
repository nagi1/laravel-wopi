<?php

namespace Nagi\LaravelWopi\Contracts;

interface ConfigRepositoryInterface
{
    public function getWopiServerUrl(): string;

    public function getDiscoveryXMLConfigFile(): ?string;

    public function getAccessTokenTTL(): int;

    public function getEnableProofValidation(): bool;

    public function supportDelete(): bool;

    public function supportUpdate(): bool;

    public function supportRename(): bool;

    public function supportLocks(): bool;
}
