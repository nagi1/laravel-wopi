<?php

namespace Nagi\LaravelWopi\Support;

use DateTimeImmutable;
use DateTimeInterface;

class DotNetTimeConverter
{
    private const MULTIPLIER = 1e7;

    private const OFFSET = 621355968e9;

    public static function toDatetime(string $ticks): DateTimeInterface
    {
        return DateTimeImmutable::createFromFormat(
            'U',
            (string) ((int) (((float) $ticks - self::OFFSET) / self::MULTIPLIER))
        );
    }

    public static function toTicks(DateTimeInterface $datetime): string
    {
        return (string) (int) (($datetime->getTimestamp() * self::MULTIPLIER) + self::OFFSET);
    }
}
