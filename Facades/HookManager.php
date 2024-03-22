<?php

namespace UniDeal\LaravelHookable\Facades;

use Illuminate\Support\Facades\Facade;
use UniDeal\LaravelHookable\Hook;

/**
 * @method static void registerHook(Hook|string $hook)
 * @method static void registerInterceptor(string $class)
 * @method static array<Hook|string> getHooks()
 * @method static array<int,callable[]> getInterceptorsForHook(Hook|string $hook)
 * @method static string getCacheFilepath()
 * @method static void loadCache()
 * @method static void createCache()
 * @method static void clearCache()
 * @method static bool isCached()
 */
class HookManager extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'hooks_manager';
    }
}
