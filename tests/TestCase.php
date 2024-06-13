<?php

namespace QuixLabs\LaravelHookSystem\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use QuixLabs\LaravelHookSystem\Providers\ServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');
    }
}
