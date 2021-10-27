<?php

namespace Nagi\LaravelWopi\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Nagi\LaravelWopi\Contracts\ConfigRepositoryInterface;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class DefaultConfigRepository implements ConfigRepositoryInterface
{
    public function getEnableProofValidation(): bool
    {
        return config('wopi.enable_proof_validation', true);
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
        return config('wopi.access_token_ttl', 0);
    }

    public function getDiscoveryXMLConfigFile(): ?string
    {
        // Todo normalize path
        $url = "{$this->getWopiServerUrl()}/hosting/discovery";
        $response = Http::get($url);

        if ($response->status() !== 200) {
            // Todo create not found exception
            throw new NotFoundResourceException("Could not reach to the configuration xml file from {$url}");
        }

        return $response->body();
    }
}
