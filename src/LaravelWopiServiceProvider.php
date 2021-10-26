<?php

namespace Nagi\LaravelWopi;

use Nagi\LaravelWopi\Contracts\ConfigRepositoryInterface;
use Nagi\LaravelWopi\Contracts\DocumentManagerInterface;
use Nagi\LaravelWopi\Contracts\WopiInterface;
use Nagi\LaravelWopi\Services\Discovery;
use Nagi\LaravelWopi\Services\ProofValidator;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelWopiServiceProvider extends PackageServiceProvider
{
    public function packageRegistered()
    {
        $this->app->bind(
            ConfigRepositoryInterface::class,
            $this->app['config']['wopi.config_repository']
        );

        $this->app->bind(Discovery::class);

        $this->app->bind(ProofValidator::class);

        // todo add this to config
        $this->app->bind(WopiInterface::class, LaravelWopi::class);

        // todo document manager swap form config
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-wopi')
            ->hasRoute('wopi.php')
            ->hasConfigFile();
    }
}
