<?php

namespace Nagi\LaravelWopi\Contracts\Concerns;

interface DisableExport
{
    /**
     * Indicates the WOPI client should disable all export.
     * functionality in libreoffice online backend. If
     * true, HideExportOption is assumed to be true.
     */
    public function disableExport(): bool;

    /**
     * Hides Download as option in the file menubar.
     */
    public function hideExportOption(): bool;
}
