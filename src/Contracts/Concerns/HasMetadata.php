<?php

namespace Nagi\LaravelWopi\Contracts\Concerns;

interface HasMetadata
{
    /**
     * The last time that the file was modified. This time must always
     * be a must be a UTC time, and formatted in ISO-8601 roundtrip
     * format. For example, "2009-06-15T13:45:30.0000000Z".
     *
     * @default-value not null empty string
     */
    public function lastModifiedTime(): string;
}
