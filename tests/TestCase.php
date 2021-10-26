<?php

namespace Nagi\LaravelWopi\Tests;

use Nagi\LaravelWopi\Contracts\ConfigRepositoryInterface;
use Nagi\LaravelWopi\Contracts\DocumentManagerInterface;
use Nagi\LaravelWopi\LaravelWopiServiceProvider;
use Nagi\LaravelWopi\Tests\Implementations\TestDocumentManager;
use Nagi\LaravelWopi\Tests\Implementations\TestingConfigRepositroy;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Factory::guessFactoryNamesUsing(
        //     fn (string $modelName) => 'Nagi\\LaravelWopi\\Database\\Factories\\'.class_basename($modelName).'Factory'
        // );
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

        // bind document manager
        $app->bind(
            DocumentManagerInterface::class,
            TestDocumentManager::class
        );

        /*
        $migration = include __DIR__.'/../database/migrations/create_laravel-wopi_table.php.stub';
        $migration->up();
        */
    }
}
