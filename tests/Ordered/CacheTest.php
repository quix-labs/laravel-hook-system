<?php

use QuixLabs\LaravelHookSystem\Facades\HookManager;
use QuixLabs\LaravelHookSystem\HookRegistry;
use Workbench\App\Hooks\GetString;
use Workbench\App\Interceptors\AppendRandomString;

test('Can generate cache using facade', function () {
    HookManager::createCache();
    HookManager::reloadCache();
    expect(HookManager::isCached())->toBeTrue();
});

test('Can clear cache using facade', function () {
    HookManager::createCache();
    HookManager::reloadCache();
    expect(HookManager::isCached())->toBeTrue();
    HookManager::clearCache();
    HookManager::reloadCache();

    expect(HookManager::isCached())->toBeFalse();
});

test('Remove hook when has no interceptors', function () {
    HookRegistry::registerHook(GetString::class);
    HookManager::createCache();
    HookManager::reloadCache();
    expect(HookManager::getHooks())->not->toContain(GetString::class);
});

test('Can cache single interceptor', function () {
    HookRegistry::registerHook(GetString::class);
    HookRegistry::registerInterceptor(AppendRandomString::class);
    HookManager::createCache();
    HookManager::reloadCache();
    expect(HookManager::getInterceptorsForHook(GetString::class))->toHaveCount(1);
});

test('Can cache same interceptor twice', function () {
    HookRegistry::registerHook(GetString::class);
    HookRegistry::registerInterceptor(AppendRandomString::class);
    HookRegistry::registerInterceptor(AppendRandomString::class);
    HookManager::createCache();
    HookManager::reloadCache();
    expect(collect(HookManager::getInterceptorsForHook(GetString::class))->flatten(1))->toHaveCount(2);
});

test('Cache are restored on boot if exists', function () {
    HookManager::createCache();
    $this->app->forgetInstance('hooks_manager');
    HookManager::clearResolvedInstances();
    expect(HookManager::isCached())->toBeTrue();
});

test('Corrupted cache file will be removed', function () {
    file_put_contents(HookManager::getCacheFilepath(), '<?php return teer;');
    HookManager::reloadCache();
    expect(HookManager::isCached())->toBeFalse();
});
