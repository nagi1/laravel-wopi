---
id: document-manager
title: Document Manager
sidebar_position: 3
---

Document Manager is the class (**worker**) and the glue between your application **(Wopi Host)** and the **Wopi Client**. It Also control the abilities for both users that currently editing the document and the document itself.

The main player here is the `AbstractDocumentManager` class which implies by the name that it's canned be called on constructed you need to implement your own document manager. This guid will help you with just that.

## Implement your own Document Manager

Let's set up a common scenario where your application stores files in the database and the actual documents (docx, pptx, xlsx) on the filesystem, Hence the name `DBDocumentManager`.

Extend `AbstractDocumentManager` and implement required methods.

You can also enable or disable features by implementing corespondant interface.

```php
use Nagi\LaravelWopi\Contracts\AbstractDocumentManager;

class DBDocumentManager extends AbstractDocumentManager
{
    // ..implement abstract methods
}

```

## Example document manager implementation

Start implementing...

```php
use Nagi\LaravelWopi\Contracts\AbstractDocumentManager;

class DBDocumentManager extends AbstractDocumentManager
{
    // Not part of the interface
    protected File $file;

    // specific to this implementation and Not part of the interface
    public function __construct(File $file)
    {
        $this->file = $file;
    }

    public static function find(string $fileId): AbstractDocumentManager
    {
        $file =  File::findorFail($fileId);
        return new static($file);
    }

    public static function findByName(string $filename): AbstractDocumentManager
    {
        $file = File::whereName($filename)->firstOrFail();
        return new static($file);
    }

    public static function create(array $properties): AbstractDocumentManager
    {
        $file = File::create([
            'name' => $properties['basename'],
            'size' => $properties['size'],
            'path' => $properties['basename'],
            'lock' => '',
            'version' => '1',
            'extension' => $properties['extension'],
            'user_id' => 1,
        ]);

        file_put_contents(Storage::disk('public')->path($properties['basename']), $properties['content']);

        return new static($file);
    }

    // Get file id
    public function id(): string
    {
        return $this->file->id;
    }

    // Wopi client requires this!
    public function userFriendlyName(): string
    {
        $user = Auth::user();
        
        // You can also use `$this->accessToken` to resolve the username using the access token.

        return is_null($user) ? 'Guest' : $user->name;
    }

    public function basename(): string
    {
        return $this->file->name;
    }

    public function owner(): string
    {
        return $this->file->user->id;
    }

    public function size(): int
    {
        return $this->file->size;
    }

    public function version(): string
    {
        return $this->file->version;
    }

    public function content(): string
    {
        return file_get_contents(Storage::disk('public')->path($this->file->path));
    }

    public function isLocked(): bool
    {
        return !empty($this->file->lock);
    }

    public function getLock(): string
    {
        return $this->file->lock;
    }

    public function put(string $content, array $editorsIds = []): void
    {
        // calculate content size and hash, be carefull with large contents!
        $size = strlen($content);
        $hash = base64_encode(hash('sha256', $content, true));
        $newVersion = uniqid();

        file_put_contents(Storage::disk('public')->path($this->file->path), $content);
        $this->file->fill(['size' => $size, 'hash' => $hash, 'version' => $newVersion])->update();
    }

    public function deleteLock(): void
    {
        $this->file->fill(['lock' => ''])->update();
    }

    public function lock(string $lockId): void
    {
        $this->file->fill(['lock' => $lockId])->update();
    }

    public function delete(): void
    {
        Storage::disk('public')->delete($this->file->path);
        $this->file->delete();
    }

}

```

We chose not to use the contractor and save it for you to inject whatever you want, in this instance we used it to inject file model from the static constructors methods (`find`, `findByName`, `create`).

It's relatively simple and straight forward to implement one of these and it's highly dependable on your needs and usecase.

## Enabling Features

Most of the wopi features encapsulated on separate interfaces, every feature have it's set of rules and complications.

## Available Features

:::info
Bellow every interface example of implementation not a package specific thing.
:::

### Delete Document

Control ability to delete documents.

Implement `Deletable` interface

For example:

```php
use Nagi\LaravelWopi\Contracts\AbstractDocumentManager;
use Nagi\LaravelWopi\Contracts\Concerns\Deletable;

class DBDocumentManager extends AbstractDocumentManager implements Deletable
{
    /**
     * Delete the document.
     */
    public function delete(): void
    {
        Storage::disk('public')->delete($this->file->path);
        $this->file->delete();
    }

    // supportDelete() already implemented for you!
    // see AbstractDocumentManager
}
```

### Rename Document

Control ability to rename documents.

Implement `Renameable` interface

For example:

```php
use Nagi\LaravelWopi\Contracts\AbstractDocumentManager;
use Nagi\LaravelWopi\Contracts\Concerns\Renameable;

class DBDocumentManager extends AbstractDocumentManager implements Renameable
{
    public function rename(string $newName): void
    {
        $oldPath = $this->file->path;
        $this
            ->file
            ->fill(['name' => "{$newName}.{$this->file->extension}", 'path' => "{$newName}.{$this->file->extension}"])
            ->update();

        $newPath = $this->file->path;

        Storage::disk('public')->move($oldPath, $newPath);
    }

    public function canUserRename(): bool
    {
        return true;
    }

    // supportRename() already implemented for you!
}
```

### Hash

Support hashes.

:::caution
Be extra carefull when calculating hashes for large contnet!
:::

Implement `HasHash` interface

For example:

```php
use Nagi\LaravelWopi\Contracts\AbstractDocumentManager;
use Nagi\LaravelWopi\Contracts\Concerns\HasHash;

class DBDocumentManager extends AbstractDocumentManager implements HasHash
{
    /**
     * A 256 bit SHA-2-encoded hash of the file contents, as Base64-encoded
     * string. Used for caching purposes in WOPI clients. be careful when
     * calculating hashes for huge files that might impact performance.
     *
     * @default-value not null empty string
     */
    public function sha256Hash(): string
    {
        return $this->file->hash;
    }
}

```

### Metadata

Add support for extra metadata about the document.

Currently on one method is available.

Implement `HasMetadata` interface

For example:

```php
use Nagi\LaravelWopi\Contracts\AbstractDocumentManager;
use Nagi\LaravelWopi\Contracts\Concerns\HasMetadata;

class DBDocumentManager extends AbstractDocumentManager implements HasMetadata
{
    /**
     * The last time that the file was modified. This time must always
     * be a must be a UTC time, and formatted in ISO-8601 roundtrip
     * format. For example, "2009-06-15T13:45:30.0000000Z".
     *
     * @default-value not null empty string
     */
    public function lastModifiedTime(): string
    {
        return Carbon::parse($this->file->updated_at, 'UTC')->toIso8601String();
    }
}
```

### Override Permissions

Control:
 - wither document is in readonly mode.
 - permission to create new files on the server.

Implement `OverridePermissions` interface

For example:

```php
use Nagi\LaravelWopi\Contracts\AbstractDocumentManager;
use Nagi\LaravelWopi\Contracts\Concerns\OverridePermissions;

class DBDocumentManager extends AbstractDocumentManager implements OverridePermissions
{
    /**
     * Indicates that, for this user, the file cannot be changed.
     *
     * @default-value false
     */
    public function isReadOnly(): bool
    {
        return auth()->user()->exceedEditingLimit();
    }

    /**
     * Indicates the user does't have permission to create new files on the
     * server. Setting this to true tells the WOPI client that calls to
     * PutRelativeFile will fail for this user on the current file.
     *
     * @default-value false
     */
    public function userCanNotWriteRelative(): bool
    {
        return auth()->user()->can('create-new-file');
    }
}
```

### Sharing

Enable sharing documents via url like in google docs sharing functionality.

Implement `Shareable` interface

```php
use Nagi\LaravelWopi\Contracts\AbstractDocumentManager;
use Nagi\LaravelWopi\Contracts\Concerns\Shareable;

class DBDocumentManager extends AbstractDocumentManager implements Shareable
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
     *
     * @possible-value ReadWrite This type of Share URL allows
     * users to both view and edit the file using the URL.
     *
     * @default-value empty array
     */
    public function supportedShareUrlTypes(): array;
}
```

### Override Urls Proprties

Control:
 - Override closing url.
 - Override download url.
 - Override get file version.

Implement `HasUrlProprties` interface

```php
use Nagi\LaravelWopi\Contracts\AbstractDocumentManager;
use Nagi\LaravelWopi\Contracts\Concerns\HasUrlProprties;

class DBDocumentManager extends AbstractDocumentManager implements HasUrlProprties
{
    /**
     * A URI to a web page that the WOPI client should
     * navigate to when the application closes, or
     * in the event of an unrecoverable error.
     *
     * @default-value not null empty string
     */
    public function closeUrl(): string;

    /**
     * A user-accessible URI to the file that allows the user to
     * download a latest version of the file. This URI should
     * directly download the file. not direct to another UI.
     *
     * @default-value not null empty string
     */
    public function downloadUrl(): string;

    /**
     * A URI to a location that allows the user to
     * view the version history for the file.
     *
     * @default-value not null empty string
     */
    public function getFileVersionUrl(): string;
}
```

### Override GetFile action url

Override the url that WOPI clients will use to get the file.

Implement `OverrideGetFileContentUrlAction` interface

```php
use Nagi\LaravelWopi\Contracts\AbstractDocumentManager;
use Nagi\LaravelWopi\Contracts\Concerns\OverrideGetFileContentUrlAction;

class DBDocumentManager extends AbstractDocumentManager implements OverrideGetFileContentUrlAction
{
    /**
     * A URI to the file location that the WOPI client uses to get the file.
     * WOPI client may use this URI to get the file instead of a GetFile
     * request. set this property if it provides better performance to
     * serve files from a different domain than current handling one.
     *
     * @see https://docs.microsoft.com/en-us/microsoft-365/cloud-storage-partner-program/rest/files/checkfileinfo#fileurl
     *
     * @default-value not null empty string
     */
    public function getFileContentUrl(): string;
}
```

### Override Get file extension

The name is pretty obvious 😂

Implement `StopRelayingOnBaseNameToGetFileExtension` interface

For example:

```php
use Nagi\LaravelWopi\Contracts\AbstractDocumentManager;
use Nagi\LaravelWopi\Contracts\Concerns\StopRelayingOnBaseNameToGetFileExtension;

class DBDocumentManager extends AbstractDocumentManager implements StopRelayingOnBaseNameToGetFileExtension
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
```

### Enable userInfo functionality

Store/retrive basic information about the user form wopi client.

Implement `InteractsWithUserInfo` interface

```php
use Nagi\LaravelWopi\Contracts\AbstractDocumentManager;
use Nagi\LaravelWopi\Contracts\Concerns\InteractsWithUserInfo;

class DBDocumentManager extends AbstractDocumentManager implements InteractsWithUserInfo
{
    /**
     * Store the string information about the user received from the wopi client.
     */
    public static function putUserInfo(string $userInfo, ?string $fileId, ?string $accessToken): void;

    /**
     * A string containing information about the user. WOPI clients can passed
     * to the host by using PutUserInfo operation. If the host has a UserInfo
     * string for the user, they must include it in this property.
     */
    public function getUserInfo(): string;

    /**
     * Wither to enable or disable this functionality.
     * Note that in case enabled you'll have to
     * implement putUserInfo and getUserInfo.
     */
    public function supportUserInfo(): bool;
}
```

### Disable features on demand

In some usecases you may want to disable one or multiple features for a certain user or document.

Implement one or more of the following interfaces:

 - `DisableCopy`
 - `DisableExport`
 - `DisablePrint`

```php
use Nagi\LaravelWopi\Contracts\AbstractDocumentManager;
use Nagi\LaravelWopi\Contracts\Concerns\DisableExport;
use Nagi\LaravelWopi\Contracts\Concerns\DisableCopy;
use Nagi\LaravelWopi\Contracts\Concerns\DisablePrint;

class DBDocumentManager extends AbstractDocumentManager implements DisableExport, DisablePrint, DisableCopy
{
    /**
     * Disables copying from the document in wopi host online backend.
     * Pasting into the document would still be possible. However,
     * it is still possible to do an “internal” cut/copy/paste.
     */
    public function disableCopy(): bool;

    /**
     * Indicates the WOPI client should disable all export.
     * functionality in WOPI host online backend. If
     * true, HideExportOption is assumed to be true.
     */
    public function disableExport(): bool;

    /**
     * Hides Download as option in the file menubar.
     */
    public function hideExportOption(): bool;

    /**
     * Indicates the WOPI client should disable all print.
     * functionality in WOPI host online backend. If
     * true, HidePrintOption is assumed to be true.
     */
    public function disablePrint(): bool;

    /**
     * If set to true, hides the print option
     * from the file menu bar in the UI.
     */
    public function hidePrintOption(): bool;

}
```

## getUrlForAction

One of the most important methods on the Document Manager, responsible for constructing the `urlsrc` for the action passed as well as override the default UI interface.

```php

   $document = app(AbstractDocumentManager::class)::find(1);

   return view('test', ['url' => $document->getUrlForAction('edit', 'ar-SA')]);

```

### Actions

Todo from the docs
