<?php

namespace QuixLabs\LaravelHookSystem\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use QuixLabs\LaravelHookSystem\Providers\ServiceProvider;

class TestCaseOrdered extends Orchestra
{
    protected function setUp(): void
    {
        if (file_exists(static::applicationBasePath() . '/bootstrap/cache/hooks.php')) {
            unlink(static::applicationBasePath() . '/bootstrap/cache/hooks.php');
        }
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
