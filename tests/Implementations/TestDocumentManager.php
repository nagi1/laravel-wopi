<?php

namespace Nagi\LaravelWopi\Tests\Implementations;

use Nagi\LaravelWopi\Contracts\DocumentManagerInterface;

class TestDocumentManager implements DocumentManagerInterface
{
    public static function find(string $fileId): static
    {
        return new static;
    }

    public function basename(): string
    {
        return 'file.text';
    }

    public function owner(): string
    {
        return 'Nagi';
    }

    public function size(): string
    {
        return 123;
    }

    public function version(): string
    {
        return 1;
    }
}
