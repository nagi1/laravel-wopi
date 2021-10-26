<?php

namespace Nagi\LaravelWopi\Support;

use Illuminate\Http\Request;
use Nagi\LaravelWopi\Contracts\WopiInterface;

class ProofValidatorInput
{
    public string $accessToken;

    public string $timestamp;

    public string $url;

    public string $proof;

    public string $oldProof;

    public function __construct(
        ?string $accessToken,
        string $timestamp,
        string $url,
        string $proof,
        string $oldProof
    ) {
        $this->accessToken = is_null($accessToken) ? RequestHelper::getAccessTokenFromUrl($url) : $accessToken;
        $this->timestamp = $timestamp;
        $this->url = $url;
        $this->proof = $proof;
        $this->oldProof = $oldProof;

        return $this;
    }

    public static function fromRequest(Request $request): static
    {
        $url = RequestHelper::parseUrl($request);
        $accessToken = RequestHelper::getAccessTokenFromUrl($url);
        $timestamp = $request->header(WopiInterface::HEADER_TIMESTAMP);
        $proofHeader = $request->header(WopiInterface::HEADER_PROOF);
        $oldProofHeader = $request->header(WopiInterface::HEADER_PROOF_OLD);

        return new static($accessToken, $timestamp, $url, $proofHeader, $oldProofHeader);
    }

    public function toArray(): array
    {
        return [
            'url' => $this->url,
            'access_token' => $this->accessToken,
            WopiInterface::HEADER_TIMESTAMP => $this->timestamp,
            WopiInterface::HEADER_PROOF => $this->proof,
            WopiInterface::HEADER_PROOF_OLD => $this->oldProof,
        ];
    }
}
