<?php

namespace Workbench\App\Providers;

use Illuminate\Support\ServiceProvider;
use QuixLabs\LaravelHookSystem\HookRegistry;
use Workbench\App\Hooks\GetString;
use Workbench\App\Interceptors\AppendRandomString;

class ExternalRegisteringProvider extends ServiceProvider
{
    public function register(): void
    {
        HookRegistry::registerInterceptor(AppendRandomString::class);
        HookRegistry::registerHook(GetString::class);
    }
}
