<?php

namespace Nagi\LaravelWopi\Tests\Implementations;

use Nagi\LaravelWopi\Contracts\AbstractDocumentManager;

class TestingDocumentManager extends AbstractDocumentManager
{
    private string $fileId;

    private string $basename;

    public function __construct(string $fileId = '123', string $basename = 'document.docx')
    {
        $this->fileId = $fileId;
        $this->basename = $basename;
    }

    public static function find(string $fileId): self
    {
        return new static($fileId);
    }

    public static function findByName(string $filename): self
    {
        return new static('123', $filename);
    }

    public static function create(array $properties): self
    {
        return new static($properties['id'] ?? '123', $properties['basename'] ?? 'document.docx');
    }

    public function id(): string
    {
        return $this->fileId;
    }

    public function userFriendlyName(): string
    {
        return 'Testing User';
    }

    public function basename(): string
    {
        return $this->basename;
    }

    public function owner(): string
    {
        return 'testing-owner';
    }

    public function size(): int
    {
        return 0;
    }

    public function version(): string
    {
        return '1';
    }

    public function content(): string
    {
        return '';
    }

    public function isLocked(): bool
    {
        return false;
    }

    public function getLock(): string
    {
        return '';
    }

    public function put(string $content, array $editorsIds = []): void
    {
        //
    }

    public function deleteLock(): void
    {
        //
    }

    public function lock(string $lockId): void
    {
        //
    }
}
