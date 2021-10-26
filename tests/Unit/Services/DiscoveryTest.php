<?php

use Nagi\LaravelWopi\Facades\Discovery;
use Nagi\LaravelWopi\Tests\TestCase;

it('can parse xml string', function () {
    /** @var TestCase $this */

    $rawXmlString = file_get_contents(__DIR__.'/discovery.xml');

    $xml = Discovery::discover($rawXmlString);

    expect($xml)->toBeInstanceOf(SimpleXMLElement::class);
});

it('can discover an action', function () {
    /** @var TestCase $this */

    $discoveredAction = Discovery::discoverAction('odt', 'edit');
    $nullAction = Discovery::discoverAction('not-existant-extension', 'edit');

    expect($discoveredAction)
    ->toBeArray()
    ->toMatchArray([
            'default' => 'true',
            'ext' => 'odt',
            'name' => 'edit',
            'urlsrc' => 'http://127.0.0.1:9980/loleaflet/d12ab86/loleaflet.html?',
            'app' => 'writer',
            'favIconUrl' => 'http://127.0.0.1:9980/loleaflet/d12ab86/images/x-office-document.svg',
    ]);

    expect($nullAction)->toBeNull();
});

it('can discover an extension', function () {
    /** @var TestCase $this */

    $discoveredExtension = Discovery::discoverExtension('odt');
    $notExistExtension = Discovery::discoverExtension('not-existant-extension');

    expect($discoveredExtension)
    ->toBeArray()
    ->toMatchArray([
        [
            'default' => 'true',
            'ext' => 'odt',
            'name' => 'writer',
            'urlsrc' => 'http://127.0.0.1:9980/loleaflet/d12ab86/loleaflet.html?',
            'favIconUrl' => 'http://127.0.0.1:9980/loleaflet/d12ab86/images/x-office-document.svg',
        ], ]);

    expect($notExistExtension)->toBeEmpty();
});

it('can discover Mime types', function () {
    /** @var TestCase $this */

    $discoveredMimeType = Discovery::discoverMimeType('application/vnd.oasis.opendocument.text');

    $notExistMimeType = Discovery::discoverExtension('not-existant-mime-type');

    expect($discoveredMimeType)
    ->toBeArray()
    ->toMatchArray([
        [
            'default' => 'true',
            'ext' => '',
            'name' => 'application/vnd.oasis.opendocument.text',
            'urlsrc' => 'http://127.0.0.1:9980/loleaflet/d12ab86/loleaflet.html?',
          ],
    ]);

    expect($notExistMimeType)->toBeEmpty();
});

it('can get capabilities url', function () {
    /** @var TestCase $this */

    $capabilitiesUrl = Discovery::getCapabilitiesUrl();

    expect($capabilitiesUrl)
    ->toBeString()
    ->toBe('http://127.0.0.1:9980/hosting/capabilities');
});

it('can get current and old public keys', function () {
    /** @var TestCase $this */

    $currentPublicKey = Discovery::getPublicKey();
    $oldPublicKey = Discovery::getOldPublicKey();

    expect($currentPublicKey)
    ->toBeString()
    ->toBe('value_BgIAAACkAABSU0ExAAgAAAEAAQDnoUzUmRfaRqZP65/QgY/6q6LJSMqYXNX/6Mi+SaGuzPRzMr0ZPlsNaarkHsaIrkhO11QPg9dUsW0pjJCw75y9OynHcc/7q/3hO9F/GLpC9NkW/eePWjZXj12JmSmKo5OkXHjmW2N3vIWGLzL3Qx47NtLyoWkhF55Qsq/z3y27epuzg1E2kYF2ki0rUjTqe2Kwx7evgj2ezTWHz+/2qI0C13KUyDLIfUvDkXmei+sXLIH/cHMA83itdL1hZLJfPS88Z3dUov5Ro9X5jaL1vFxpew0uEvqXD+Peu67UujmKbOOYK29HLI5brq4TkIr+LTAxZu3THqqLvM1wtJQkV++4');

    expect($oldPublicKey)
    ->toBeString()
    ->toBe('oldvalue_BgIAAACkAABSU0ExAAgAAAEAAQDnoUzUmRfaRqZP65/QgY/6q6LJSMqYXNX/6Mi+SaGuzPRzMr0ZPlsNaarkHsaIrkhO11QPg9dUsW0pjJCw75y9OynHcc/7q/3hO9F/GLpC9NkW/eePWjZXj12JmSmKo5OkXHjmW2N3vIWGLzL3Qx47NtLyoWkhF55Qsq/z3y27epuzg1E2kYF2ki0rUjTqe2Kwx7evgj2ezTWHz+/2qI0C13KUyDLIfUvDkXmei+sXLIH/cHMA83itdL1hZLJfPS88Z3dUov5Ro9X5jaL1vFxpew0uEvqXD+Peu67UujmKbOOYK29HLI5brq4TkIr+LTAxZu3THqqLvM1wtJQkV++4');
});

it('can get current and old proof exponent', function () {
    /** @var TestCase $this */

    $currentExponent = Discovery::getProofExponent();
    $oldExponent = Discovery::getOldProofExponent();

    expect($currentExponent)
    ->toBeString()
    ->toBe('AQAB');

    expect($oldExponent)
    ->toBeString()
    ->toBe('AQAB');
});

it('can get current and old proof modulus', function () {
    /** @var TestCase $this */

    $currentModulus = Discovery::getProofModulus();
    $oldModulus = Discovery::getOldProofModulus();

    expect($currentModulus)
    ->toBeString()
    ->toBe('uO9XJJS0cM28i6oe0+1mMTAt/oqQE66uW44sR28rmONsijm61K673uMPl/oSLg17aVy89aKN+dWjUf6iVHdnPC89X7JkYb10rXjzAHNw/4EsF+uLnnmRw0t9yDLIlHLXAo2o9u/PhzXNnj2Cr7fHsGJ76jRSKy2SdoGRNlGDs5t6uy3f86+yUJ4XIWmh8tI2Ox5D9zIvhoW8d2Nb5nhcpJOjiimZiV2PVzZaj+f9Ftn0QroYf9E74f2r+89xxyk7vZzvsJCMKW2xVNeDD1TXTkiuiMYe5KppDVs+Gb0yc/TMrqFJvsjo/9VcmMpIyaKr+o+B0J/rT6ZG2heZ1Eyh5w==');

    expect($oldModulus)
    ->toBeString()
    ->toBe('uO9XJJS0cM28i6oe0+1mMTAt/oqQE66uW44sR28rmONsijm61K673uMPl/oSLg17aVy89aKN+dWjUf6iVHdnPC89X7JkYb10rXjzAHNw/4EsF+uLnnmRw0t9yDLIlHLXAo2o9u/PhzXNnj2Cr7fHsGJ76jRSKy2SdoGRNlGDs5t6uy3f86+yUJ4XIWmh8tI2Ox5D9zIvhoW8d2Nb5nhcpJOjiimZiV2PVzZaj+f9Ftn0QroYf9E74f2r+89xxyk7vZzvsJCMKW2xVNeDD1TXTkiuiMYe5KppDVs+Gb0yc/TMrqFJvsjo/9VcmMpIyaKr+o+B0J/rT6ZG2heZ1Eyh5w==');
});
