<?php

namespace QuixLabs\LaravelHookSystem\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use QuixLabs\LaravelHookSystem\Providers\ServiceProvider;
use Workbench\App\Providers\WorkbenchServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            WorkbenchServiceProvider::class,
            ServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
    }
}
