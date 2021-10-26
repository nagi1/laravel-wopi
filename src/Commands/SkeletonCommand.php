<?php

namespace Nagi\LaravelWopi\Commands;

use Illuminate\Console\Command;

class LaravelWopiCommand extends Command
{
    public $signature = 'laravel-wopi';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
