---
id: installation
title: Installation
sidebar_position: 1
---

Start by installing the package via composer

```bash
composer require nagi/laravel-wopi
```

## Requirements {#requirements}

- Php >= 7.4 or above.
- Mbstring extension.
- XML extension
- Installed and configured WOPI client.

:::tip
**Don't have WOPI Client yet?** Follow this guide to install [Collabora Online](https://sdk.collaboraonline.com/docs/installation/CODE_Docker_image.html).
:::

## 1-Config

Publish the required config file using by

```bash
php artisan vendor:publish --tag=wopi-config
```

You can view all available confugration options and full explanation in the [Configuration Section](configuration.md).

Set `WOPI_CLIENT_URL` in your `.env` file with full url to your wopi client.

For example:

```env
WOPI_CLIENT_URL="https://demo.eu.collaboraonline.com"
```

## 2-Implement document manager

`DocumentManager` is responsible for storing, retriving, accessing documents.

Every application has it's own implementation of how it handles documents, It's pretty much impossible to implement one general purpose document manager that fits all usecases. So you **Need** to implement your own `DocumentManager` but don't you worry this package provides a `AbstractDocumentManager` that will ease your task quite a bit.

Take this example implementation from [Laravel wopi example](https://github.com/nagi1/wopi-host-example).

- See [Document Manager Section](document-manager#example-document-manager-implementation) for more details about `AbstractDocumentManager`.

## 3-User your document manager

It's important to let the package know the default document manager implementation.

```php

// config/wopi.php

     /*
     * Managing documents differs a lot between apps, because of this reason
     * this configration left empty to be implemented by the user There's
     * plans to implement example database manager in the future though.
     */
    'document_manager' =>  App\Services\DBDocumentManager::class,

```

## 4-Build view with iframe

Add simple html view using the technology stack you prefer to existing website. On the website, you need to present an iframe where the editing UI and the document itself will be present.

For example

```html
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel Wopi</title>
    <!-- Styles -->
    <style type="text/css">
        #office_frame {
            width: 100%;
            height: 800px;
            margin: 0;
            border: none;
            display: block;
        }
    </style>
</head>

<body>
    <form id="office_form" name="office_form" target="office_frame" action="{!! $url !!}" method="post">
        <input name="access_token" value="{!! $accessToken !!}" type="text" />
        <input name="access_token_ttl" value="{!! $ttl !!}" type="text" />
    </form>

    <span id="frameholder"></span>

    <script type="text/javascript">
        var frameholder = document.getElementById('frameholder');
            var office_frame = document.createElement('iframe');
            office_frame.name = 'office_frame';
            office_frame.id = 'office_frame';
            // The title should be set for accessibility
            office_frame.title = 'Office Frame';
            // This attribute allows true fullscreen mode in slideshow view
            // when using PowerPoint's 'view' action.
            office_frame.setAttribute('allowfullscreen', 'true');
            // The sandbox attribute is needed to allow automatic redirection to the O365 sign-in page in the business user flow
            office_frame.setAttribute(
                'sandbox',
                'allow-scripts allow-same-origin allow-forms allow-popups allow-top-navigation allow-popups-to-escape-sandbox allow-downloads allow-modals'
            );
            frameholder.appendChild(office_frame);
            document.getElementById('office_form').submit();
    </script>
</body>

</html>

```

## 5-Retrive your document

Query your document manager to get any [supported Document](#) like so

```php
// In web.php/your controller
Route::get('/', function (Request $request) {
    $document = app(AbstractDocumentManager::class)::find(1);

    // Implementing access tokens is left to you!
    $accessToken = 'My_Token';
    // The TTL actually is an expiry, a unix timestamp in milliseconds.
    $ttl = (time() + 60*60) * 1000;

    return view('laravel-wopi-test', [
        'accessToken' => $accessToken,
        'ttl' => $ttl,
        'url' => $document->generateUrl()
        ]);
});

```

Open your application and voalla!

![Logo](/img/office_docx_app.png)

You have your self a working google docs in the comfort of your app!

## Problems? {#problems}

Ask for help on [Stack Overflow](https://stackoverflow.com/questions/tagged/laravel-wopi), on our [GitHub repository](https://github.com/nagi1/laravel-wopi) or [Twitter](https://twitter.com/nagiworks).
