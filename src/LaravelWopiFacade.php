<?php

namespace Nagi\LaravelWopi;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Nagi\LaravelWopi\
 */
class LaravelWopiFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return LaravelWopi::class;
    }
}
