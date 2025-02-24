<?php

namespace Nagi\LaravelWopi\Contracts\Concerns;

/**
 * Provides information to allow showing breadcrumbs in the editor. Not necessarily supported by all WOPI clients.
 */
interface HasBreadcrumbs
{
    /**
     * The name of the brand URL.
     */
    public function breadcrumbBrandName(): string;

    /**
     * A user accessible URI that allows the user to
     * go back to the root page.
     */
    public function breadcrumbBrandUrl(): string;

    /**
     * The name of the document, optional.
     */
    public function breadcrumbDocName(): string;

    /**
     * The name of the folder URL
     */
    public function breadcrumbFolderName(): string;

    /**
     * A user accessible URI that allows the user to
     * * go back to the parent of the document.
     */
    public function breadcrumbFolderUrl(): string;
}
