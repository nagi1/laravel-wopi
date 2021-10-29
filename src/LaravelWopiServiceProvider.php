<?php

namespace Nagi\LaravelWopi;

use Nagi\LaravelWopi\Contracts\AbstractDocumentManager;
use Nagi\LaravelWopi\Contracts\ConfigRepositoryInterface;
use Nagi\LaravelWopi\Contracts\WopiInterface;
use Nagi\LaravelWopi\Http\Requests\WopiRequest;
use Nagi\LaravelWopi\Services\Discovery;
use Nagi\LaravelWopi\Services\ProofValidator;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelWopiServiceProvider extends PackageServiceProvider
{
    public function packageRegistered()
    {
        $this->app->singleton(
            ConfigRepositoryInterface::class,
            $this->app['config']['wopi.config_repository']
        );

        $this->app->singleton(WopiInterface::class, $this->app['config']['wopi.wopi_implementation']);

        $this->app->bind(AbstractDocumentManager::class, $this->app['config']['wopi.document_manager']);

        $this->app->bind(WopiRequest::class, $this->app['config']['wopi.wopi_request']);

        $this->app->bind(Discovery::class);

        $this->app->bind(ProofValidator::class);
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-wopi')
            ->hasRoute('wopi')
            ->hasConfigFile();
    }
}
