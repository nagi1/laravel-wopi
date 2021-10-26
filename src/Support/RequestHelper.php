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
     * Alias for getAccessTokenFromUrl.
     */
    public static function parseAccessToken(Request $request): ?string
    {
        $url = static::parseUrl($request);

        return static::getAccessTokenFromUrl($url);
    }

    /**
     * Extract only access_token from url.
     */
    public static function getAccessTokenFromUrl(string $url): ?string
    {
        preg_match("/\?access_token=\K[^&]+/", $url, $matches);

        return optional($matches)[0];
    }
}
