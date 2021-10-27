<?php

namespace Nagi\LaravelWopi\Contracts;

use Closure;

abstract class AbstractDocumentManager
{
    /**
     * No properties should be set to null. If you do not wish
     * to set a property, simply omit it from the response
     * and WOPI clients will use the default value.
     */
    protected static array $propertyMethodMapping = [
        // Required proprties
        'FileBaseName' => 'basename',
        'OwnerId' => 'owner',
        'Size' => 'size',
        'Version' => 'version',
        'UserId' => 'userId',

        // Permission properties
        'ReadOnly' => 'isReadOnly',
        'UserCanNotWriteRelative' => 'canUserWriteRelative',
        'UserCanRename' => 'canUserRename',
        'UserCanWrite' => 'canUserWrite',

        // File URl proprties
        'CloseUrl' => 'closeUrl',
        'DownloadUrl' => 'downloadUrl',
        'FileVersionUrl' => 'getFileVersionUrl',

        // Sharable
        'FileSharingUrl' => 'sharingUrl',

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
    ];

    /**
     * Resloved User Id.
     *
     * @var string|Closure
     */
    protected $userId = '';

    /**
     * Preform look up for the file/document.
     *
     * @param string $fileId unique ID, Represent a single file and URL safe.
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    abstract public static function find(string $fileId): static;

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
     * Manually set user id.
     */
    public function setUserId(string $userId): static
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
            $userId = call_user_func($this->userId);

            return empty($userId) ? $defaultUserId : $userId;
        }

        return empty($this->userId) ? $defaultUserId : $this->userId;
    }

    /**
     * When there's no user id this value will be used.
     */
    protected function defaultUser(): string
    {
        return 'Unknown User';
    }

    /**
     * Manually set user id using closure.
     */
    public function getUserUsing(Closure $calback): static
    {
        $this->userId = $calback;

        return $this;
    }

    /**
     * Get CheckfileInfo response proprites based
     * on implemented interfaces/features.
     */
    public function getResponseProprties(): array
    {
        return  collect(static::$propertyMethodMapping)
                ->flatMap(function (string $methodName, string $propertyName) {
                    if (method_exists($this, $methodName)) {
                        return [
                             $propertyName => $this->$methodName(),
                        ];
                    }
                })
                ->toArray();
    }
}
