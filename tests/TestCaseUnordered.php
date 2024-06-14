<?php

namespace QuixLabs\LaravelHookSystem\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use QuixLabs\LaravelHookSystem\Providers\ServiceProvider;
use Workbench\App\Providers\ExternalRegisteringProvider;

class TestCaseUnordered extends TestCaseOrdered
{
    protected function getPackageProviders($app): array
    {
        return [
            ExternalRegisteringProvider::class,
            ServiceProvider::class,
        ];
    }

}
