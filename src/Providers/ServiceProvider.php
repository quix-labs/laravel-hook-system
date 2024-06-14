<?php

namespace QuixLabs\LaravelHookSystem\Providers;

use Illuminate\Foundation\Console\AboutCommand;
use QuixLabs\LaravelHookSystem\Console\Commands\HooksCacheCommand;
use QuixLabs\LaravelHookSystem\Console\Commands\HooksClearCommand;
use QuixLabs\LaravelHookSystem\Console\Commands\HooksStatusCommand;
use QuixLabs\LaravelHookSystem\Facades\HookManager as HookManagerFacade;
use QuixLabs\LaravelHookSystem\HookManager;
use QuixLabs\LaravelHookSystem\HookRegistry;
use QuixLabs\LaravelHookSystem\Hooks\GetHooksTable;
use Workbench\App\Hooks\GetStringFullyCacheable;
use Workbench\App\Interceptors\AppendRandomStringFullyCacheable;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('hooks_manager', function () {
            return new HookManager();
        });
        $this->_registerCommands();
        $this->_registerHooks();
    }

    public function boot(): void
    {
        $this->_bootHooksRegistry();
        $this->_appendInformationToAboutCommand();
    }

    private function _registerHooks(): void
    {
        HookRegistry::registerHook(GetHooksTable::class);
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

    private function _bootHooksRegistry(): void
    {
        if (! HookManagerFacade::isCached()) {
            foreach (HookRegistry::getHooks() as $hook) {
                HookManagerFacade::registerHook($hook);
            }
            foreach (HookRegistry::getInterceptors() as $interceptor) {
                HookManagerFacade::registerInterceptor($interceptor);
            }
        }
        HookRegistry::clear();
    }
}
