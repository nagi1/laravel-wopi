<?php

namespace Nagi\LaravelWopi\Facades;

use Illuminate\Support\Facades\Facade;
use Nagi\LaravelWopi\Services\Discovery as WopiDiscovery;

/**
 * @method static \SimpleXMLElement discover(string $rawXmlString)
 * @method static null|array discoverAction(string $extension, string $name = 'view')
 * @method static array discoverExtension(string $extension)
 * @method static array discoverMimeType(string $mimeType)
 * @method static string getCapabilitiesUrl()
 * @method static string getPublicKey()
 * @method static string getOldPublicKey()
 * @method static string getProofExponent()
 * @method static string getOldProofExponent()
 * @method static string getProofModulus()
 * @method static string getOldProofModulus()
 *
 * @see \Nagi\LaravelWopi\Services\Discovery
 */
class Discovery extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return WopiDiscovery::class;
    }
}
