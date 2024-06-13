<?php

namespace QuixLabs\LaravelHookSystem\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Console\AboutCommand;
use QuixLabs\LaravelHookSystem\Console\Commands\HooksCacheCommand;
use QuixLabs\LaravelHookSystem\Console\Commands\HooksClearCommand;
use QuixLabs\LaravelHookSystem\Console\Commands\HooksStatusCommand;
use QuixLabs\LaravelHookSystem\Facades\HookManager as HookManagerFacade;
use QuixLabs\LaravelHookSystem\HookManager;
use QuixLabs\LaravelHookSystem\Hooks\GetHooksTable;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
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
        AboutCommand::add('Cache', fn () => [
            'Hooks' => HookManagerFacade::isCached()
                    ? '<fg=green;options=bold>CACHED</>'
                    : '<fg=yellow;options=bold>NOT CACHED</>',
        ]);
    }
}
