<?php

use Nagi\LaravelWopi\Services\Discovery as DiscoveryService;
use Nagi\LaravelWopi\Tests\Implementations\TestingConfigRepositroy;
use Nagi\LaravelWopi\Tests\TestCase;

function countingConfigRepository(int $ttl = 3600, ?string $rawXmlString = null): TestingConfigRepositroy
{
    return new class($ttl, $rawXmlString) extends TestingConfigRepositroy {
        public int $fetchCount = 0;

        public function __construct(private int $ttl, private ?string $rawXmlString)
        {
        }

        public function getDiscoveryXMLConfigFile(): ?string
        {
            $this->fetchCount++;

            return $this->rawXmlString ?? parent::getDiscoveryXMLConfigFile();
        }

        public function getDiscoveryCacheTtl(): int
        {
            return $this->ttl;
        }
    };
}

it('retrieves the discovery document once and serves it from the cache', function () {
    /** @var TestCase $this */

    $config = countingConfigRepository();

    (new DiscoveryService($config))->forget();

    (new DiscoveryService($config))->getPublicKey();
    (new DiscoveryService($config))->getProofModulus();

    expect($config->fetchCount)->toBe(1);
});

it('retrieves the discovery document for every instance when caching is disabled', function () {
    /** @var TestCase $this */

    $config = countingConfigRepository(ttl: 0);

    (new DiscoveryService($config))->getPublicKey();
    (new DiscoveryService($config))->getPublicKey();

    expect($config->fetchCount)->toBe(2);
});

it('retrieves the discovery document again after forgetting it', function () {
    /** @var TestCase $this */

    $config = countingConfigRepository();

    $discovery = new DiscoveryService($config);
    $discovery->forget();
    $discovery->getPublicKey();

    $discovery->forget();
    $discovery->getPublicKey();

    expect($config->fetchCount)->toBe(2);
});

it('does not cache a discovery document that can not be parsed', function () {
    /** @var TestCase $this */

    // What a proxy in front of the client answers with when it redirects.
    $config = countingConfigRepository(rawXmlString: '<html><body>301 Moved Permanently</body></html>');

    (new DiscoveryService($config))->forget();

    expect(fn () => (new DiscoveryService($config))->getPublicKey())->toThrow(Exception::class);
    expect(fn () => (new DiscoveryService($config))->getPublicKey())->toThrow(Exception::class);

    expect($config->fetchCount)->toBe(2);
});

it('clears the cached discovery document using the artisan command', function () {
    /** @var TestCase $this */

    $config = countingConfigRepository();

    app()->instance(DiscoveryService::class, $discovery = new DiscoveryService($config));

    $discovery->getPublicKey();

    $this->artisan('wopi:clear-discovery')->assertSuccessful();

    (new DiscoveryService($config))->getPublicKey();

    expect($config->fetchCount)->toBe(2);
});
