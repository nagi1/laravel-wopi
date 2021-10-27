<?php

namespace Nagi\LaravelWopi\Contracts\Concerns;

interface DisablePrint
{
    /**
     * Indicates the WOPI client should disable all print.
     * functionality in libreoffice online backend. If
     * true, HidePrintOption is assumed to be true.
     */
    public function disablePrint(): bool;

    /**
     * If set to true, hides the print option
     * from the file menu bar in the UI.
     */
    public function hidePrintOption(): bool;
}
