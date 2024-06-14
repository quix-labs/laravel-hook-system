<?php

namespace QuixLabs\LaravelHookSystem\Facades;

use Illuminate\Support\Facades\Facade;
use QuixLabs\LaravelHookSystem\Hook;

/**
 * @method static void registerHook(Hook|string $hook)
 * @method static void registerInterceptor(string $class)
 * @method static array<Hook|string> getHooks()
 * @method static array<int, callable[]> getInterceptorsForHook(Hook|string $hook)
 * @method static string getCacheFilepath()
 * @method static void reloadCache()
 * @method static void createCache()
 * @method static void clearCache()
 * @method static bool isCached()
 * @method static bool isFullyCached(Hook|string $hook)
 * @method static bool isFullyCacheable(Hook|string $hook)
 * @method static Hook|null getCachedHook(Hook|string $hook)
 */
class HookManager extends Facade
{

    protected static function getFacadeAccessor(): string
    {
        return 'hooks_manager';
    }
}
