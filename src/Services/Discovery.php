<?php

namespace Nagi\LaravelWopi\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Nagi\LaravelWopi\Contracts\ConfigRepositoryInterface;
use SimpleXMLElement;

class Discovery
{
    private ConfigRepositoryInterface $config;

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
                ->discover($this->config->getDiscoveryXMLConfigFile())
                ->xpath($expression);

        if (! $appElements) {
            throw new Exception('Could not find app element make sure to have the proper configuration file.');
        }

        return $appElements;
    }

    public function discover(string $rawXmlString): SimpleXMLElement
    {
        // Todo create cache manager to determine how and when the cache is busted
        $simpleXmlElement = simplexml_load_string($rawXmlString);

        if (! $simpleXmlElement) {
            // TODO make proper exception
            throw new Exception('Unable to parse the XML in "discovery.xml" file.');
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
        // Todo cache keys with the cache manager
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
