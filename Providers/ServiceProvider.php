<?php

namespace UniDeal\LaravelHookable\Providers;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Console\AboutCommand;
use UniDeal\LaravelHookable\Console\Commands\HooksCacheCommand;
use UniDeal\LaravelHookable\Console\Commands\HooksClearCommand;
use UniDeal\LaravelHookable\Console\Commands\HooksStatusCommand;
use UniDeal\LaravelHookable\Facades\HookManager as HookManagerFacade;
use UniDeal\LaravelHookable\HookManager;
use UniDeal\LaravelHookable\Contracts\HookManager as HookManagerContract;
use UniDeal\LaravelHookable\Hooks\GetHooksTable;

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
        $this->app->singleton(HookManagerContract::class, function (Application $app) {
            return new HookManager();
        });

        $hookManager = $this->app->make(HookManagerContract::class);
        $this->app->instance('hooks_manager', $hookManager);
    }

    private function _registerHooks(): void
    {
        if (!HookManagerFacade::isCached()) {
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
        if (!$this->app->runningInConsole()) {
            return;
        }
        if (HookManagerFacade::isCached()) {
            AboutCommand::add('Cache', 'Hooks', '<fg=green;options=bold>CACHED</>');
        } else {
            AboutCommand::add('Cache', 'Hooks', '<fg=yellow;options=bold>NOT CACHED</>');
        }
    }

}
