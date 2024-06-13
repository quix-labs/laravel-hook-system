<?php

namespace QuixLabs\LaravelHookSystem\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Console\AboutCommand;
use QuixLabs\LaravelHookSystem\Console\Commands\HooksCacheCommand;
use QuixLabs\LaravelHookSystem\Console\Commands\HooksClearCommand;
use QuixLabs\LaravelHookSystem\Console\Commands\HooksStatusCommand;
use QuixLabs\LaravelHookSystem\Facades\HookManager as HookManagerFacade;
use QuixLabs\LaravelHookSystem\HookManager;
use QuixLabs\LaravelHookSystem\Hooks\GetHooksTable;

class ServiceProvider extends \Illuminate\Support\ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->_registerHookManager();
        $this->_registerCommands();
        $this->_registerHooks();
    }

    public function boot(): void
    {
        $this->_appendInformationToAboutCommand();
    }

    private function _registerHookManager(): void
    {
        $this->app->singleton('hooks_manager', function (Application $app) {
            return new HookManager();
        });
    }

    private function _registerHooks(): void
    {
        if (! HookManagerFacade::isCached()) {
            HookManagerFacade::registerHook(GetHooksTable::class);
        }
    }

    private function _registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                HooksStatusCommand::class,
                HooksCacheCommand::class,
                HooksClearCommand::class,
            ]);
        }
    }

    private function _appendInformationToAboutCommand(): void
    {
        if (! $this->app->runningInConsole()) {
            return;
        }
        if (HookManagerFacade::isCached()) {
            AboutCommand::add('Cache', 'Hooks', '<fg=green;options=bold>CACHED</>');
        } else {
            AboutCommand::add('Cache', 'Hooks', '<fg=yellow;options=bold>NOT CACHED</>');
        }
    }

    public function provides(): array
    {
        return ['hooks_manager'];
    }
}
