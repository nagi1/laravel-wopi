<?php

namespace Nagi\LaravelWopi\Contracts;

use Closure;
use Exception;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Nagi\LaravelWopi\Contracts\Concerns\SupportBusinessUser;
use Nagi\LaravelWopi\Contracts\Traits\SupportLocks;
use Nagi\LaravelWopi\Facades\Discovery;

abstract class AbstractDocumentManager
{
    use SupportLocks;

    /**
     * No properties should be set to null. If you do not wish
     * to set a property, simply omit it from the response
     * and WOPI clients will use the default value.
     */
    protected static array $propertyMethodMapping = [
        // Required proprties
        'BaseFileName' => 'basename',
        'OwnerId' => 'owner',
        'Size' => 'size',
        'Version' => 'version',
        'UserId' => 'userId',
        'UserFriendlyName' => 'userFriendlyName',

        // Permission properties
        'ReadOnly' => 'isReadOnly',
        'UserCanNotWriteRelative' => 'userCanNotWriteRelative',
        'UserCanRename' => 'canUserRename',
        'UserCanWrite' => 'canUserWrite',

        // File URl proprties
        'CloseUrl' => 'closeUrl',
        'DownloadUrl' => 'downloadUrl',
        'FileVersionUrl' => 'getFileVersionUrl',

        // Sharable
        'FileSharingUrl' => 'sharingUrl',
        'SupportedShareUrlTypes' => 'supportedShareUrlTypes',

        // Override getting file content url
        'FileUrl' => 'getFileContentUrl',

        // Override getting file extension logic
        'FileExtension' => 'extension',

        // Meta data
        'LastModifiedTime' => 'lastModifiedTime',

        // hash
        'SHA256' => 'sha256Hash',

        // Disable Printing
        'DisablePrint' => 'disablePrint',
        'HidePrintOption' => 'hidePrintOption',

        // Disable Exporing
        'DisableExport' => 'disableExport',
        'HideExportOption' => 'hideExportOption',

        // Disable copy
        'DisableCopy' => 'disableCopy',

        // Interacts with user info
        'UserInfo' => 'getUserInfo',
        'SupportsUserInfo' => 'supportUserInfo',

        // Override supported features
        'SupportsDeleteFile' => 'supportDelete',
        'SupportsLocks' => 'supportLocks',
        'SupportsGetLock' => 'supportGetLock',
        'SupportsUpdate' => 'supportUpdate',
        'SupportsRename' => 'supportRename',
        'SupportsExtendedLockLength' => 'supportExtendedLockLength',

        // Breadcrumbs
        'BreadcrumbBrandName' => 'breadcrumbBrandName',
        'BreadcrumbBrandUrl' => 'breadcrumbBrandUrl',
        'BreadcrumbDocName' => 'breadcrumbDocName',
        'BreadcrumbFolderName' => 'breadcrumbFolderName',
        'BreadcrumbFolderUrl' => 'breadcrumbFolderUrl',

        // Business user
        'LicenseCheckForEditIsEnabled' => 'licenseCheckForEditIsEnabled',
        'HostEditUrl' => 'hostEditUrl',
    ];

    /**
     * Resloved User Id.
     *
     * @var string|Closure
     */
    protected $userId = '';

    /**
     * The access token.
     * Note: This is not assured to be set {@see \Nagi\LaravelWopi\LaravelWopi}!
     *
     * @var string|null
     */
    protected $accessToken = null;

    /**
     * Preform look up for the file/document.
     *
     * @param  string  $fileId  unique ID, Represent a single file and URL safe.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    abstract public static function find(string $fileId): self;

    /**
     * Preform look up for the file/document by filename.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    abstract public static function findByName(string $filename): self;

    /**
     * Create new document instace on the host.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    abstract public static function create(array $properties): self;

    /**
     * Unique id that identifies single file could be numbers
     * or string, but also should be url safe. It should
     * match fileId parameter passed to static::find.
     */
    abstract public function id(): string;

    /**
     * Friendly name for current edting user.
     */
    abstract public function userFriendlyName(): string;

    public function supportUpdate(): bool
    {
        /** @var ConfigRepositoryInterface */
        $config = app(ConfigRepositoryInterface::class);

        return $config->supportUpdate();
    }

    public function supportRename(): bool
    {
        /** @var ConfigRepositoryInterface */
        $config = app(ConfigRepositoryInterface::class);

        return $config->supportRename();
    }

    public function supportDelete(): bool
    {
        /** @var ConfigRepositoryInterface */
        $config = app(ConfigRepositoryInterface::class);

        return $config->supportDelete();
    }

    /**
     * Name of the file, including extension, without a path. Used
     * for display in user interface (UI), and determining
     * and  determining the extension of the file.
     */
    abstract public function basename(): string;

    /**
     * Uniquely identifies the owner of the file. In most
     * cases, the user who uploaded or created the file
     * should be considered the owner.
     */
    abstract public function owner(): string;

    /**
     * The size of the file in bytes, expressed
     * as a long, a 64-bit signed integer.
     */
    abstract public function size(): int;

    /**
     * The current version of the file based on the server’s file
     * version schema, as a string. This value must change when
     * the file changes, and version values must never repeat.
     */
    abstract public function version(): string;

    /**
     * Binary contents of the file. Not the url!
     */
    abstract public function content(): string;

    /**
     * Determin if the document is locked or not.
     */
    abstract public function isLocked(): bool;

    /**
     * Get current lock on the document.
     */
    abstract public function getLock(): string;

    /**
     * Change document contents.
     */
    abstract public function put(string $content, array $editorsIds = []): void;

    /**
     * Delete the lock on the document.
     */
    abstract public function deleteLock(): void;

    /**
     * Lock the document prevent it from being altered or deleted.
     */
    abstract public function lock(string $lockId): void;

    /**
     * Manually set user id.
     */
    public function setUserId(string $userId): self
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Value uniquely identifying the user currently accessing the
     * file. Can be set to the current logged user ideally.
     */
    public function userId(): string
    {
        $defaultUserId = $this->defaultUser();

        if ($this->userId instanceof Closure) {
            $userId = call_user_func($this->userId, $this);

            return empty($userId) ? $defaultUserId : $userId;
        }

        return (string) empty($this->userId) ? $defaultUserId : $this->userId;
    }

    /**
     * When there's no user id this value will be used.
     */
    protected function defaultUser(): string
    {
        /** @var ConfigRepositoryInterface */
        $config = app(ConfigRepositoryInterface::class);

        return $config->getDefaultUser();
    }

    /**
     * Indicates that the user has permission to alter the
     * file. Setting this to true tells the WOPI client
     * that it can call PutFile on behalf of the user.
     *
     * @default-value false
     */
    public function canUserWrite(): bool
    {
        return true;
    }

    /**
     * Manually set user id using closure.
     */
    public function getUserUsing(Closure $calback): self
    {
        $this->userId = $calback;

        return $this;
    }

    /**
     * Convenient method for getUrlForAction.
     */
    public function generateUrl(string $lang = null, string $action = 'edit'): string
    {
        /** @var ConfigRepositoryInterface */
        $config = app(ConfigRepositoryInterface::class);
        $lang = empty($lang) ? $config->getDefaultUiLang() : $lang;

        return $this->getUrlForAction($action, $lang, $config->isMicrosoft365Enabled());
    }

    public function getUrlForAction(string $action, string $lang, bool $isMicrosoft365Enabled): string
    {
        $extension = method_exists($this, 'extension')
            ? Str::replaceFirst('.', '', $this->extension())
            : pathinfo($this->basename(), PATHINFO_EXTENSION);

        /** @var ConfigRepositoryInterface */
        $config = app(ConfigRepositoryInterface::class);

        if ($config->getEnableInteractiveWopiValidation()) {
            $action = 'view';
            $extension = 'wopitest';
        }

        $actionUrl = optional(Discovery::discoverAction($extension, $action));

        $hasHostOverride = $config->getWopiHostUrl() ? true : false;
        if ($hasHostOverride) {
            URL::forceRootUrl($config->getWopiHostUrl());
        }
        $url = route('wopi.checkFileInfo', [
            'file_id' => $this->id(),
        ]);
        if ($hasHostOverride) {
            URL::forceRootUrl(null);
        }

        if (is_null($actionUrl['urlsrc'])) {
            throw new Exception("Unsupported action \"{$action}\" for \"{$extension}\" extension.");
        }

        $urlsrc = (string) $actionUrl['urlsrc'];

        // Kept as a separate entry point so existing overrides of it keep
        // being applied, both branches build the url the same way.
        if ($isMicrosoft365Enabled) {
            return $this->processMicrosoftOffice365Url($urlsrc, $url, $lang);
        }

        return $this->buildActionUrl($urlsrc, $url, $lang);
    }

    /**
     * Turn the "urlsrc" template advertised by the discovery document into
     * the url the browser gets pointed at: fill in the placeholder groups
     * we can supply, drop the ones we can't and append what is missing.
     *
     * Templates look like this, the trailing groups being optional:
     * https://excel.officeapps.live.com/x/_layouts/xlviewerinternal.aspx?<ui=UI_LLCC&><rs=DC_LLCC&><dchat=DISABLE_CHAT&><hid=HOST_SESSION_ID&><sc=SESSION_CONTEXT&><wopisrc=WOPI_SOURCE&><IsLicensedUser=BUSINESS_USER&><actnavid=ACTIVITY_NAVIGATION_ID&>
     */
    protected function buildActionUrl(string $urlsrc, string $wopiSrc, string $lang): string
    {
        /** @var ConfigRepositoryInterface */
        $config = app(ConfigRepositoryInterface::class);

        // The wopi source has to be encoded exactly once.
        $encodedWopiSrc = rawurlencode($wopiSrc);

        $requiredReplaceMap = [
            'UI_LLCC' => $lang,
            'DC_LLCC' => $lang,
            'WOPI_SOURCE' => $encodedWopiSrc,
        ];

        if ($this instanceof SupportBusinessUser) {
            $requiredReplaceMap['BUSINESS_USER'] = $this->licenseCheckForEditIsEnabled() ? 1 : 0;
        }

        // The required placeholders take precedence over the configured ones.
        $replaceMap = array_merge($config->getMicrosoft365UrlPlaceholderValueMap(), $requiredReplaceMap);

        $resolved = [];

        $url = preg_replace_callback('/<([^>]*)>/', function (array $matches) use ($replaceMap, &$resolved) {
            $queryParamWithPlaceholder = $matches[1];

            foreach ($replaceMap as $placeholder => $value) {
                if (! str_contains($queryParamWithPlaceholder, (string) $placeholder)) {
                    continue;
                }

                if ((string) $value === '') {
                    return '';
                }

                $resolved[$placeholder] = true;

                return str_replace((string) $placeholder, (string) $value, $queryParamWithPlaceholder);
            }

            // Nothing to supply for this one, e.g. <hid=HOST_SESSION_ID&>.
            return '';
        }, $urlsrc);

        $url = rtrim((string) $url, '&?');

        $query = [];

        // Clients that don't advertise a language placeholder still
        // understand the plain "lang" query parameter.
        if ($lang !== '' && ! isset($resolved['UI_LLCC']) && ! isset($resolved['DC_LLCC'])) {
            $query[] = 'lang='.rawurlencode($lang);
        }

        if (! isset($resolved['WOPI_SOURCE'])) {
            $query[] = 'WOPISrc='.$encodedWopiSrc;
        }

        if (empty($query)) {
            return $url;
        }

        return $url.(str_contains($url, '?') ? '&' : '?').implode('&', $query);
    }

    /**
     * Placeholders are resolved for every client now, this only remains
     * as an override point for hosts that replaced it.
     */
    protected function processMicrosoftOffice365Url(string $url, string $wopiSrc, string $lang): string
    {
        return $this->buildActionUrl($url, $wopiSrc, $lang);
    }

    /**
     * Get CheckfileInfo response proprites based
     * on implemented interfaces/features.
     */
    public function getResponseProprties(): array
    {
        $response = collect(static::$propertyMethodMapping)
            ->flatMap(function (string $methodName, string $propertyName) {
                if (method_exists($this, $methodName)) {
                    return [
                        $propertyName => $this->$methodName(),
                    ];
                }
            })
            ->filter(fn ($value) => $value !== null)
            ->toArray();

        /** @var ConfigRepositoryInterface */
        $config = app(ConfigRepositoryInterface::class);

        if ($config->getEnableInteractiveWopiValidation()) {
            $response['BaseFileName'] = 'wopitest.wopitest';
            $response['FileExtension'] = '.wopitest';
        }

        return $response;
    }

    /**
     * Just sets the access token.
     */
    public function setAccessToken(string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }
}
