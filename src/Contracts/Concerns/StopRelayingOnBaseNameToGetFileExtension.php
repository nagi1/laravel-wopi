<?php

namespace Nagi\LaravelWopi\Contracts\Concerns;

interface StopRelayingOnBaseNameToGetFileExtension
{
    /**
     * Get the file extension. This value must begin with a dot (.) If provided, WOPI
     * clients will use this value as the file extension. Otherwise the extension
     * will be parsed from the BaseFileName. not required but recommended.
     *
     * @default-value not null empty string
     */
    public function extension(): string;
}
