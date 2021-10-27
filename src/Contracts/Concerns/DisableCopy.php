<?php

namespace Nagi\LaravelWopi\Contracts\Concerns;

interface DisableCopy
{
    /**
     * Disables copying from the document in libreoffice online backend.
     * Pasting into the document would still be possible. However,
     * it is still possible to do an “internal” cut/copy/paste.
     */
    public function disableCopy(): bool;
}
