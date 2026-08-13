<?php

namespace Nagi\LaravelWopi\Commands;

use Illuminate\Console\Command;
use Nagi\LaravelWopi\Services\Discovery;

class ClearDiscoveryCacheCommand extends Command
{
    protected $signature = 'wopi:clear-discovery';

    protected $description = 'Clear the cached discovery document of the wopi client';

    public function handle(Discovery $discovery): int
    {
        $discovery->forget();

        $this->info('Wopi discovery cache cleared.');

        return self::SUCCESS;
    }
}
