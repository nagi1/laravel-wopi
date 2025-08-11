<?php

namespace Nagi\LaravelWopi\Contracts\Concerns;

/**
 * Provides information to indicate and relevant for business users. This is used in Microsoft 365 and required for
 * business users.
 */
interface SupportBusinessUser
{
    /**
     * True for business users.
     */
    public function licenseCheckForEditIsEnabled(): bool;

    /**
     * The edit URL of the WOPI host.
     */
    public function hostEditUrl(): string;
}
