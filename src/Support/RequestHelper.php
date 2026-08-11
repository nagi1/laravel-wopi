<?php

namespace Nagi\LaravelWopi\Support;

use Illuminate\Http\Request;

class RequestHelper
{
    /**
     *  Extract full raw url without any normalization.
     */
    public static function parseUrl(Request $request): string
    {
        // Laravel uses html_entity_decode internally to escape certain
        // characters from the query string this approach parses the
        // access_token directly from the request without escape.
        $rawQueryString = $request->server('QUERY_STRING');

        return "{$request->url()}?{$rawQueryString}";
    }

    /**
     * The access token of the request, either from the query string or
     * from the authorization header, whichever the client used.
     */
    public static function parseAccessToken(Request $request): ?string
    {
        $url = static::parseUrl($request);

        return static::getAccessTokenFromUrl($url)
            ?? static::getAccessTokenFromHeader($request);
    }

    /**
     * Extract only access_token from url. Clients are free to order their
     * query parameters as they like, so the token is looked up by name
     * rather than expected in a fixed position.
     */
    public static function getAccessTokenFromUrl(?string $url): ?string
    {
        if (empty($url)) {
            return null;
        }

        $separator = strpos($url, '?');

        if ($separator === false) {
            return null;
        }

        // Values are deliberately not decoded, the proof validation has to
        // hash the token exactly as the client sent it.
        foreach (explode('&', substr($url, $separator + 1)) as $queryParam) {
            if (! str_starts_with($queryParam, 'access_token=')) {
                continue;
            }

            $accessToken = substr($queryParam, strlen('access_token='));

            return $accessToken === '' ? null : $accessToken;
        }

        return null;
    }

    /**
     * Extract the access token from the authorization header, which is what
     * clients send instead of the query parameter when they are told to,
     * e.g. `wopi.sendAuthorizationHeader` on OnlyOffice document server.
     */
    public static function getAccessTokenFromHeader(Request $request): ?string
    {
        $authorization = (string) $request->header('Authorization');

        if (! preg_match('/^\s*Bearer\s+(\S+)\s*$/i', $authorization, $matches)) {
            return null;
        }

        return $matches[1];
    }
}
