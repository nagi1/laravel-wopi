<?php

namespace Nagi\LaravelWopi\Contracts\Concerns;

interface HasUrlProprties
{
    /**
     * A URI to a web page that the WOPI client should
     * navigate to when the application closes, or
     * in the event of an unrecoverable error.
     *
     * @default-value not null empty string
     */
    public function closeUrl(): string;

    /**
     * A user-accessible URI to the file that allows the user to
     * download a latest version of the file. This URI should
     * directly download the file. not direct to another UI.
     *
     * @default-value not null empty string
     */
    public function downloadUrl(): string;

    /**
     * A URI to a location that allows the user to
     * view the version history for the file.
     *
     * @default-value not null empty string
     */
    public function getFileVersionUrl(): string;
}
