<?php

namespace Nagi\LaravelWopi\Tests;

use Nagi\LaravelWopi\Contracts\AbstractDocumentManager;
use Nagi\LaravelWopi\Contracts\ConfigRepositoryInterface;
use Nagi\LaravelWopi\LaravelWopiServiceProvider;
use Nagi\LaravelWopi\Tests\Implementations\TestDocumentManager;
use Nagi\LaravelWopi\Tests\Implementations\TestingConfigRepositroy;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelWopiServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        // bind config to testing properties
        $app->bind(
            ConfigRepositoryInterface::class,
            TestingConfigRepositroy::class
        );

        $app->bind(
            AbstractDocumentManager::class,
            TestDocumentManager::class
        );
    }
}
