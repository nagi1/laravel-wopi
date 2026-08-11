<?php

namespace Nagi\LaravelWopi\Services;

use Exception;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Support\Facades\Cache;
use Nagi\LaravelWopi\Contracts\ConfigRepositoryInterface;
use SimpleXMLElement;

class Discovery
{
    public const CACHE_KEY = 'laravel-wopi.discovery';

    private ConfigRepositoryInterface $config;

    /**
     * Parsed discovery document, memoized for this instance.
     */
    private ?SimpleXMLElement $document = null;

    public function __construct(
        ConfigRepositoryInterface $config
    ) {
        $this->config = $config;
    }

    /**
     * @return  false|SimpleXMLElement[]|null
     */
    private function queryXPath(string $expression)
    {
        /** @var $appElements */
        $appElements = $this
                ->document()
                ->xpath($expression);

        if (! $appElements) {
            throw new Exception('Could not find app element make sure to have the proper configuration file.');
        }

        return $appElements;
    }

    /**
     * The discovery document of the wopi client. Retrieving it usually
     * means an http roundtrip, so the raw xml gets cached.
     */
    public function document(): SimpleXMLElement
    {
        if ($this->document !== null) {
            return $this->document;
        }

        $ttl = $this->config->getDiscoveryCacheTtl();

        if ($ttl <= 0) {
            return $this->document = $this->discover((string) $this->config->getDiscoveryXMLConfigFile());
        }

        $rawXmlString = $this->cache()->get(static::CACHE_KEY);

        if (is_string($rawXmlString) && $rawXmlString !== '') {
            return $this->document = $this->discover($rawXmlString);
        }

        $rawXmlString = (string) $this->config->getDiscoveryXMLConfigFile();

        // Parse before caching, otherwise an error page served by a proxy
        // in front of the client sticks around for the whole ttl.
        $this->document = $this->discover($rawXmlString);

        $this->cache()->put(static::CACHE_KEY, $rawXmlString, $ttl);

        return $this->document;
    }

    /**
     * Forget the cached discovery document. Wopi clients move their action
     * urls between versions, so this has to run whenever the client gets
     * upgraded, otherwise stale urls are handed to the browser.
     */
    public function forget(): void
    {
        $this->document = null;

        $this->cache()->forget(static::CACHE_KEY);
    }

    private function cache(): CacheRepository
    {
        return Cache::store($this->config->getDiscoveryCacheStore());
    }

    public function discover(string $rawXmlString): SimpleXMLElement
    {
        $useInternalErrors = libxml_use_internal_errors(true);

        $simpleXmlElement = simplexml_load_string($rawXmlString);

        $errors = libxml_get_errors();
        libxml_clear_errors();
        libxml_use_internal_errors($useInternalErrors);

        if (! $simpleXmlElement) {
            $error = reset($errors);
            $reason = $error ? trim($error->message) : 'unknown reason';

            throw new Exception("Unable to parse the \"discovery.xml\" file: {$reason}.");
        }

        // Guards against parsing something that happens to be valid xml but
        // is not a discovery document, like the error page of a proxy
        // sitting in front of the wopi client.
        if ($simpleXmlElement->getName() !== 'wopi-discovery') {
            throw new Exception("Expected a \"wopi-discovery\" document but got \"{$simpleXmlElement->getName()}\", make sure the client url points to a wopi client.");
        }

        return $simpleXmlElement;
    }

    public function discoverAction(string $extension, string $name = 'edit'): ?array
    {
        $appElements = $this->queryXPath('//net-zone/app');

        $return = [];

        foreach ($appElements as $app) {
            $actions = $app->xpath(sprintf('action[@ext="%s" and @name="%s"]', $extension, $name));

            if (! $actions) {
                continue;
            }

            foreach ($actions as $action) {
                $actionAttributes = $action->attributes() ?: [];

                $return[] = array_merge(
                    (array) reset($actionAttributes),
                    ['app' => (string) $app['name']],
                    ['favIconUrl' => (string) $app['favIconUrl']]
                );
            }
        }

        $action = current($return);

        return ! $action ? null : $action;
    }

    public function discoverExtension(string $extension): array
    {
        $appElements = $this->queryXPath('//net-zone/app');

        $extensions = [];

        foreach ($appElements as $app) {
            $actions = $app->xpath(sprintf("action[@ext='%s']", $extension));

            if (! $actions) {
                continue;
            }

            foreach ($actions as $action) {
                $actionAttributes = $action->attributes() ?: [];

                $extensions[] = array_merge(
                    (array) reset($actionAttributes),
                    ['name' => (string) $app['name']],
                    ['favIconUrl' => (string) $app['favIconUrl']]
                );
            }
        }

        return $extensions;
    }

    public function discoverAvilableActions(): array
    {
        $appElements = $this->queryXPath('//net-zone/app');

        $extensions = [];

        foreach ($appElements as $app) {
            $actions = $app->xpath('action[@ext]');

            if (! $actions) {
                continue;
            }

            foreach ($actions as $action) {
                $actionAttributes = $action->attributes() ?: [];

                $extensions[] = array_merge(
                    (array) reset($actionAttributes),
                    ['name' => (string) $app['name']],
                    ['favIconUrl' => (string) $app['favIconUrl']]
                );
            }
        }

        return $extensions;
    }

    public function discoverMimeType(string $mimeType): array
    {
        $appElements = $this->queryXPath(sprintf("//net-zone/app[@name='%s']", $mimeType));

        $mimeTypes = [];

        foreach ($appElements as $app) {
            $actions = $app->xpath('action');

            if (! $actions) {
                continue;
            }

            foreach ($actions as $action) {
                $actionAttributes = $action->attributes() ?: [];

                $mimeTypes[] = array_merge(
                    (array) reset($actionAttributes),
                    ['name' => (string) $app['name']],
                );
            }
        }

        return $mimeTypes;
    }

    public function getCapabilitiesUrl(): string
    {
        $capabilities = $this->queryXPath("//net-zone/app[@name='Capabilities']");

        if ($capabilities === false) {
            return '';
        }

        $capabilities = reset($capabilities);

        return $capabilities->action['urlsrc'];
    }

    public function getPublicKey(): string
    {
        return (string) $this->queryXPath('//proof-key/@value')[0];
    }

    public function getOldPublicKey(): string
    {
        return (string) $this->queryXPath('//proof-key/@oldvalue')[0];
    }

    public function getProofModulus(): string
    {
        return (string) $this->queryXPath('//proof-key/@modulus')[0];
    }

    public function getProofExponent(): string
    {
        return (string) $this->queryXPath('//proof-key/@exponent')[0];
    }

    public function getOldProofModulus(): string
    {
        return (string) $this->queryXPath('//proof-key/@oldmodulus')[0];
    }

    public function getOldProofExponent(): string
    {
        return (string) $this->queryXPath('//proof-key/@oldexponent')[0];
    }
}
