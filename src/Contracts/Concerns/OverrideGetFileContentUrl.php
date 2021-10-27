<?php

namespace Nagi\LaravelWopi\Contracts\Concerns;

interface OverrideGetFileContentUrl
{
    /**
     * A URI to the file location that the WOPI client uses to get the file.
     * WOPI client may use this URI to get the file instead of a GetFile
     * request. set this property if it provides better performance to
     * serve files from a different domain than current handling one.
     *
     * @see https://docs.microsoft.com/en-us/microsoft-365/cloud-storage-partner-program/rest/files/checkfileinfo#fileurl
     *
     * @default-value not null empty string
     */
    public function getFileContentUrl(): string;
}
