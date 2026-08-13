# Changelog

All notable changes to `laravel-wopi` will be documented in this file.

## Unreleased

- Look the `access_token` up by name anywhere in the query string. It used to be
  matched only directly after the `?`, so any parameter a client put in front of
  it made the token come out empty.
- Accept the token from an `Authorization: Bearer` header as well, which is what
  clients send with `wopi.sendAuthorizationHeader` enabled.
- `ProofValidatorInput` properties are nullable. A request without a token or
  proof headers used to raise a `TypeError` instead of failing validation.

- Resolve the `<PLACEHOLDER&>` groups of the advertised `urlsrc` for every wopi
  client instead of Office 365 only. Clients such as newer OnlyOffice document
  server builds advertise `<wopisrc=WOPI_SOURCE&>`, which used to be passed to
  the browser verbatim, next to a second unencoded `WOPISrc` parameter.
- `WOPISrc` is url encoded exactly once.
- Cache the discovery document, see the `discovery_cache_ttl` and
  `discovery_cache_store` options. Clear it with `php artisan wopi:clear-discovery`
  after upgrading the wopi client.
- Fail loudly when the discovery url answers with something other than a
  `wopi-discovery` document, e.g. the error page of a proxy.
- **Breaking:** `ConfigRepositoryInterface` gained `getDiscoveryCacheTtl()` and
  `getDiscoveryCacheStore()`, custom config repositories have to implement them.

## 1.0.0 - 202X-XX-XX

- initial release
