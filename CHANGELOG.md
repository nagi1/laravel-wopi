# Changelog

All notable changes to `laravel-wopi` will be documented in this file.

## Unreleased

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
