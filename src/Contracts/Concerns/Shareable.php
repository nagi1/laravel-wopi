<?php

namespace Nagi\LaravelWopi\Contracts\Concerns;

interface Shareable
{
    /**
     * A URI to a location that allows the user to share the file.
     *
     * @default-value not null empty string
     */
    public function sharingUrl(): string;
}
