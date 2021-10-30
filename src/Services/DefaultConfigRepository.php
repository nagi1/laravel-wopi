<?php

namespace Nagi\LaravelWopi\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Nagi\LaravelWopi\Contracts\ConfigRepositoryInterface;

class DefaultConfigRepository implements ConfigRepositoryInterface
{
    public function supportDelete(): bool
    {
        return config('wopi.support_delete');
    }

    public function supportUpdate(): bool
    {
        return config('wopi.support_update');
    }

    public function supportRename(): bool
    {
        return config('wopi.support_rename');
    }

    public function supportLocks(): bool
    {
        return config('wopi.support_locks');
    }

    public function getEnableProofValidation(): bool
    {
        return config('wopi.enable_proof_validation');
    }

    public function getWopiServerUrl(): string
    {
        return config('wopi.server_url');
    }

    public function getWopiServerPort(): string
    {
        return config('wopi.server_port');
    }

    public function getAccessTokenTTL(): int
    {
        return config('wopi.access_token_ttl');
    }

    public function getDiscoveryXMLConfigFile(): ?string
    {
        // Todo normalize path
        $url = "{$this->getWopiServerUrl()}/hosting/discovery";
        $response = Http::get($url);

        if ($response->status() !== 200) {
            // Todo create proper not found exception
            throw new Exception("Could not reach to the configuration discovery.xml file from {$url}");
        }

        return $response->body();
    }
}
