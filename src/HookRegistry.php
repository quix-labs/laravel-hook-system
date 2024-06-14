<?php

namespace QuixLabs\LaravelHookSystem;

use QuixLabs\LaravelHookSystem\Facades\HookManager;

class HookRegistry
{
    protected static array $hooks = [];

    protected static array $interceptors = [];

    public static function registerHook($hook): void
    {
        if (static::booted() === true && ! HookManager::isCached()) {
            Hookmanager::registerHook($hook);

            return;
        }
        static::$hooks[] = $hook;
    }

    public static function registerInterceptor($interceptor): void
    {
        if (static::booted() === true && ! HookManager::isCached()) {
            Hookmanager::registerInterceptor($interceptor);

            return;
        }
        static::$interceptors[] = $interceptor;
    }

    public static function getHooks(): array
    {
        return static::$hooks;
    }

    public static function getInterceptors(): array
    {
        return static::$interceptors;
    }

    public static function clear(): void
    {
        static::$hooks = [];
        static::$interceptors = [];
    }

    public static function booted(): bool
    {
        return app()->resolved('hooks_manager');
    }
}
