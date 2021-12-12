<?php

namespace Nagi\LaravelWopi\Contracts\Traits;

use Nagi\LaravelWopi\Contracts\ConfigRepositoryInterface;

trait SupportLocks
{
    public function supportLocks(): bool
    {
        /** @var ConfigRepositoryInterface */
        $config = app(ConfigRepositoryInterface::class);

        return $config->supportLocks();
    }

    public function supportGetLock(): bool
    {
        /** @var ConfigRepositoryInterface */
        $config = app(ConfigRepositoryInterface::class);

        return $config->supportGetLocks();
    }

    public function supportExtendedLockLength(): bool
    {
        /** @var ConfigRepositoryInterface */
        $config = app(ConfigRepositoryInterface::class);

        return $config->supportExtendedLockLength();
    }
}
