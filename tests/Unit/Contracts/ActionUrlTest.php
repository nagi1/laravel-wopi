<?php

use Nagi\LaravelWopi\Contracts\Concerns\SupportBusinessUser;
use Nagi\LaravelWopi\Contracts\ConfigRepositoryInterface;
use Nagi\LaravelWopi\Facades\Discovery;
use Nagi\LaravelWopi\Services\DefaultConfigRepository;
use Nagi\LaravelWopi\Services\Discovery as DiscoveryService;
use Nagi\LaravelWopi\Tests\Implementations\TestingConfigRepositroy;
use Nagi\LaravelWopi\Tests\Implementations\TestingDocumentManager;
use Nagi\LaravelWopi\Tests\TestCase;

/**
 * The wopi source of the document below, encoded the way it has to
 * end up in the url handed to the client.
 */
const ENCODED_WOPI_SRC = 'http%3A%2F%2Flocalhost%2Fwopi%2Ffiles%2F123';

function actionUrlFor(string $urlsrc, string $lang = 'en-US'): string
{
    Discovery::shouldReceive('discoverAction')
        ->once()
        ->with('docx', 'edit')
        ->andReturn(['urlsrc' => $urlsrc]);

    return TestingDocumentManager::find('123')->generateUrl($lang);
}

it('resolves the wopi source placeholder advertised by the client', function () {
    /** @var TestCase $this */

    // What newer OnlyOffice document server builds advertise.
    $url = actionUrlFor('https://ds.example.com/hosting/wopi/word/edit?<wopisrc=WOPI_SOURCE&>');

    expect($url)->toBe('https://ds.example.com/hosting/wopi/word/edit?wopisrc='.ENCODED_WOPI_SRC.'&lang=en-US');
});

it('appends the wopi source when the client advertises no placeholder for it', function () {
    /** @var TestCase $this */

    // What Collabora advertises.
    $url = actionUrlFor('http://127.0.0.1:9980/loleaflet/d12ab86/loleaflet.html?');

    expect($url)->toBe('http://127.0.0.1:9980/loleaflet/d12ab86/loleaflet.html?lang=en-US&WOPISrc='.ENCODED_WOPI_SRC);
});

it('starts the query string when the advertised url has none', function () {
    /** @var TestCase $this */

    $url = actionUrlFor('https://ds.example.com/hosting/wopi/word/edit');

    expect($url)->toBe('https://ds.example.com/hosting/wopi/word/edit?lang=en-US&WOPISrc='.ENCODED_WOPI_SRC);
});

it('drops the placeholders it can not supply', function () {
    /** @var TestCase $this */

    $url = actionUrlFor('https://excel.officeapps.live.com/x/_layouts/xlviewerinternal.aspx?<ui=UI_LLCC&><rs=DC_LLCC&><dchat=DISABLE_CHAT&><hid=HOST_SESSION_ID&><sc=SESSION_CONTEXT&><wopisrc=WOPI_SOURCE&><IsLicensedUser=BUSINESS_USER&><actnavid=ACTIVITY_NAVIGATION_ID&>');

    expect($url)->toBe('https://excel.officeapps.live.com/x/_layouts/xlviewerinternal.aspx?ui=en-US&rs=en-US&wopisrc='.ENCODED_WOPI_SRC);
});

it('resolves the configured placeholders', function () {
    /** @var TestCase $this */

    app()->bind(ConfigRepositoryInterface::class, DefaultConfigRepository::class);
    config()->set('wopi.microsoft_365_url_placeholder_value_map', [
        'BUSINESS_USER' => '1',
        'DISABLE_CHAT' => '1',
    ]);

    $url = actionUrlFor('https://excel.officeapps.live.com/x/_layouts/xlviewerinternal.aspx?<ui=UI_LLCC&><dchat=DISABLE_CHAT&><wopisrc=WOPI_SOURCE&><IsLicensedUser=BUSINESS_USER&>');

    expect($url)->toBe('https://excel.officeapps.live.com/x/_layouts/xlviewerinternal.aspx?ui=en-US&dchat=1&wopisrc='.ENCODED_WOPI_SRC.'&IsLicensedUser=1');
});

it('encodes the wopi source exactly once', function () {
    /** @var TestCase $this */

    $url = actionUrlFor('https://ds.example.com/hosting/wopi/word/edit?<wopisrc=WOPI_SOURCE&>');

    expect($url)
        ->toContain('wopisrc='.ENCODED_WOPI_SRC)
        ->not->toContain('%25');
});

it('never hands a placeholder group to the client', function () {
    /** @var TestCase $this */

    $url = actionUrlFor('https://ds.example.com/hosting/wopi/word/edit?<wopisrc=WOPI_SOURCE&><hid=HOST_SESSION_ID&>');

    expect($url)
        ->not->toContain('<')
        ->not->toContain('>')
        ->not->toContain('WOPI_SOURCE');
});

it('builds the same url through the microsoft 365 entry point', function () {
    /** @var TestCase $this */

    app()->instance(ConfigRepositoryInterface::class, new class extends TestingConfigRepositroy
    {
        public function isMicrosoft365Enabled(): bool
        {
            return true;
        }
    });

    $url = actionUrlFor('https://excel.officeapps.live.com/x/_layouts/xlviewerinternal.aspx?<ui=UI_LLCC&><wopisrc=WOPI_SOURCE&>');

    expect($url)->toBe('https://excel.officeapps.live.com/x/_layouts/xlviewerinternal.aspx?ui=en-US&wopisrc='.ENCODED_WOPI_SRC);
});

it('resolves the business user placeholder', function () {
    /** @var TestCase $this */

    Discovery::shouldReceive('discoverAction')
        ->twice()
        ->with('docx', 'edit')
        ->andReturn(['urlsrc' => 'https://excel.officeapps.live.com/x/_layouts/xlviewerinternal.aspx?<wopisrc=WOPI_SOURCE&><IsLicensedUser=BUSINESS_USER&>']);

    $document = new class extends TestingDocumentManager implements SupportBusinessUser
    {
        public bool $licensed = true;

        public function licenseCheckForEditIsEnabled(): bool
        {
            return $this->licensed;
        }

        public function hostEditUrl(): string
        {
            return 'http://localhost/documents/123/edit';
        }
    };

    $editorUrl = 'https://excel.officeapps.live.com/x/_layouts/xlviewerinternal.aspx?wopisrc='.ENCODED_WOPI_SRC;

    expect($document->generateUrl('en-US'))->toBe($editorUrl.'&IsLicensedUser=1&lang=en-US');

    $document->licensed = false;

    // The placeholder has to be sent as 0, not dropped.
    expect($document->generateUrl('en-US'))->toBe($editorUrl.'&IsLicensedUser=0&lang=en-US');
});

it('builds the url from the discovery document the client serves', function () {
    /** @var TestCase $this */

    app()->instance(ConfigRepositoryInterface::class, new class extends TestingConfigRepositroy
    {
        public function getDiscoveryXMLConfigFile(): ?string
        {
            return file_get_contents(__DIR__.'/../Services/discovery-onlyoffice.xml');
        }
    });

    Discovery::clearResolvedInstance(DiscoveryService::class);

    $url = TestingDocumentManager::find('123')->generateUrl('en-US');

    expect($url)->toBe('https://ds.example.com/hosting/wopi/word/edit?wopisrc='.ENCODED_WOPI_SRC.'&ui=en-US');
});

it('fails when the client advertises no action for the extension', function () {
    /** @var TestCase $this */

    Discovery::shouldReceive('discoverAction')
        ->once()
        ->with('docx', 'edit')
        ->andReturn(null);

    expect(fn () => TestingDocumentManager::find('123')->generateUrl('en-US'))
        ->toThrow(Exception::class, 'Unsupported action "edit" for "docx" extension.');
});
