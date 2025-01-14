<?php

namespace Nagi\LaravelWopi\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Nagi\LaravelWopi\Contracts\ConfigRepositoryInterface;

class DefaultConfigRepository implements ConfigRepositoryInterface
{
    public function supportUserInfo(): bool
    {
        return config('wopi.support_user_info');
    }

    public function getDefaultUiLang(): string
    {
        return config('wopi.ui_language');
    }

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

    public function supportGetLocks(): bool
    {
        return config('wopi.support_get_locks');
    }

    public function supportExtendedLockLength(): bool
    {
        return config('wopi.support_extended_lock_length');
    }

    public function getEnableProofValidation(): bool
    {
        return  config('wopi.enable_proof_validation');
        // return !App::isProduction() && config('wopi.enable_proof_validation');
    }

    public function getWopiClientUrl(): string
    {
        return config('wopi.client_url');
    }

    public function getWopiHostUrl(): string
    {
        return config('wopi.host_url');
    }

    public function getDefaultUser(): string
    {
        return config('wopi.default_user');
    }

    public function getAccessTokenTTL(): int
    {
        return config('wopi.access_token_ttl');
    }

    public function getMiddleware(): array
    {
        return config('wopi.middleware');
    }

    public function getDiscoveryXMLConfigFile(): ?string
    {
        $url = "{$this->getWopiClientUrl()}/hosting/discovery";
        $response = Http::get($url);

        if ($response->status() !== 200) {
            throw new Exception("Could not reach to the configuration discovery.xml file from {$url}.");
        }

        return $response->body();
    }

    public function getMicrosoft365UrlPlaceholderValueMap(): array
    {
        return config('wopi.microsoft_365_url_placeholder_value_map', []);
    }

    public function getEnableInteractiveWopiValidation(): bool
    {
        return config('wopi.enable_interactive_wopi_validation', false);
    }
}
