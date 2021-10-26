<?php

namespace Nagi\LaravelWopi\Contracts;

interface DocumentManagerInterface
{
    public static function find(string $fileId): static;

    public function basename(): string;

    public function owner(): string;

    public function size(): string;

    public function version(): string;
}
