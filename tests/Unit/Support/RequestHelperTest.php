<?php

use Illuminate\Http\Request;
use Nagi\LaravelWopi\Support\ProofValidatorInput;
use Nagi\LaravelWopi\Support\RequestHelper;
use Nagi\LaravelWopi\Tests\TestCase;

function wopiRequest(string $queryString, array $headers = []): Request
{
    $request = Request::create("http://localhost/wopi/files/123?{$queryString}", 'GET', [], [], [], $headers);

    // Request::create() does not fill the raw query string the helper reads.
    $request->server->set('QUERY_STRING', $queryString);

    return $request;
}

it('extracts the access token from the query string', function () {
    /** @var TestCase $this */

    expect(RequestHelper::parseAccessToken(wopiRequest('access_token=abc123')))->toBe('abc123');
});

it('extracts the access token from any position in the query string', function () {
    /** @var TestCase $this */

    expect(RequestHelper::parseAccessToken(wopiRequest('access_token_ttl=0&access_token=abc123')))->toBe('abc123');
    expect(RequestHelper::parseAccessToken(wopiRequest('sc=session&access_token=abc123&access_token_ttl=0')))->toBe('abc123');
});

it('does not mistake access_token_ttl for the access token', function () {
    /** @var TestCase $this */

    expect(RequestHelper::parseAccessToken(wopiRequest('access_token_ttl=1737000000000')))->toBeNull();
});

it('keeps the access token exactly as the client sent it', function () {
    /** @var TestCase $this */

    // Decoding here would break the proof validation hash.
    $token = 'abc%2F123%3D%3D';

    expect(RequestHelper::parseAccessToken(wopiRequest("access_token={$token}")))->toBe($token);
});

it('falls back to the bearer token of the authorization header', function () {
    /** @var TestCase $this */

    $request = wopiRequest('access_token_ttl=0', ['HTTP_AUTHORIZATION' => 'Bearer abc123']);

    expect(RequestHelper::parseAccessToken($request))->toBe('abc123');
});

it('prefers the query string over the authorization header', function () {
    /** @var TestCase $this */

    $request = wopiRequest('access_token=from-query', ['HTTP_AUTHORIZATION' => 'Bearer from-header']);

    expect(RequestHelper::parseAccessToken($request))->toBe('from-query');
});

it('ignores an authorization header that carries no bearer token', function () {
    /** @var TestCase $this */

    $request = wopiRequest('', ['HTTP_AUTHORIZATION' => 'Basic dXNlcjpwYXNz']);

    expect(RequestHelper::parseAccessToken($request))->toBeNull();
});

it('has no access token when neither the query string nor the header carries one', function () {
    /** @var TestCase $this */

    expect(RequestHelper::parseAccessToken(wopiRequest('')))->toBeNull();
    expect(RequestHelper::parseAccessToken(wopiRequest('access_token=')))->toBeNull();
    expect(RequestHelper::getAccessTokenFromUrl(null))->toBeNull();
    expect(RequestHelper::getAccessTokenFromUrl('http://localhost/wopi/files/123'))->toBeNull();
});

it('builds a proof validator input from a request without a token', function () {
    /** @var TestCase $this */

    // A missing token used to be assigned to a non nullable string property.
    $input = ProofValidatorInput::fromRequest(wopiRequest(''));

    expect($input->accessToken)->toBeNull()
        ->and($input->proof)->toBeNull()
        ->and($input->timestamp)->toBeNull();
});

it('reads the access token of a proof validator input from the header', function () {
    /** @var TestCase $this */

    $request = wopiRequest('access_token_ttl=0', ['HTTP_AUTHORIZATION' => 'Bearer abc123']);

    expect(ProofValidatorInput::fromRequest($request)->accessToken)->toBe('abc123');
});
