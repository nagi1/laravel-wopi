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

    /**
     * The Share URL types supported by the host. These types can
     * be passed in the X-WOPI-UrlType request header to signify
     * which Share URL type to return for the GetShareUrl.
     *
     * @possible-value ReadOnly This type of Share URL allows
     * users to view the file using the URL, but does not
     * give them permission to edit the file.
     * @possible-value ReadWrite This type of Share URL allows
     * users to both view and edit the file using the URL.
     *
     * @default-value empty array
     */
    public function supportedShareUrlTypes(): array;
}
