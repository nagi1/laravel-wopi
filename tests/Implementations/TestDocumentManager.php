<?php

namespace Nagi\LaravelWopi\Tests\Implementations;

use Nagi\LaravelWopi\Contracts\AbstractDocumentManager;

class TestDocumentManager extends AbstractDocumentManager
{
    public array $properties = [];

    public function __construct(array $properties = [])
    {
        $this->properties = $properties;

        return $this;
    }

    public static function find(string $fileId): static
    {
        $filesDatabase = collect(json_decode(file_get_contents(__DIR__.'/files.json'), true));
        $file = $filesDatabase->firstOrFail(fn (array $item) => $item['id'] === $fileId);

        return new static($file);
    }

    public static function findByName(string $filename): static
    {
        $filesDatabase = collect(json_decode(file_get_contents(__DIR__.'/files.json'), true));
        $file = $filesDatabase->firstOrFail(fn (array $item) => $item['basename'] === $filename);

        return new static($file);
    }

    public static function create(array $properties): static
    {
        return new static($properties);
    }

    protected function defaultUser(): string
    {
        $auth = auth()->id();

        return is_null($auth) ? 'Default User' : $auth;
    }

    public function id(): string
    {
        return $this->properties['id'];
    }

    public function basename(): string
    {
        return $this->properties['basename'];
    }

    public function owner(): string
    {
        return $this->properties['owner'];
    }

    public function size(): int
    {
        return $this->properties['size'];
    }

    public function version(): string
    {
        return $this->properties['version'];
    }

    public function content(): string
    {
        return $this->properties['content'];
    }

    public function isLocked(): bool
    {
        return ! is_null($this->properties['lock_id']);
    }

    public function getLock(): string
    {
        return $this->properties['lock_id'];
    }

    public function put(string $content, array $editorsIds = []): void
    {
        $this->properties['content'] = $content;
    }

    public function deleteLock(): void
    {
        $this->properties['lock_id'] = null;
    }

    public function lock(string $lockId): void
    {
        $this->properties['lock_id'] = $lockId;
    }
}
