<?php

use QuixLabs\LaravelHookSystem\Facades\HookManager;
use Workbench\App\Hooks\GetString;
use Workbench\App\Interceptors\AppendRandomString;

afterEach(function () {
    HookManager::clearCache();
    $this->app->forgetInstance('hooks_manager');
    HookManager::clearResolvedInstances();
});

test('Can generate cache using facade', function () {
    HookManager::createCache();
    expect(HookManager::isCached())->toBeTrue();
});

test('Can clear cache using facade', function () {
    HookManager::createCache();
    expect(HookManager::isCached())->toBeTrue();
    HookManager::clearCache();
    expect(HookManager::isCached())->toBeFalse();
});

test('Remove hook when has no interceptors', function () {
    HookManager::registerHook(GetString::class);
    HookManager::createCache();
    HookManager::loadCache();
    expect(HookManager::getHooks())->not->toContain(GetString::class);
});

test('Can cache single interceptor', function () {
    HookManager::registerHook(GetString::class);
    HookManager::registerInterceptor(AppendRandomString::class);
    HookManager::createCache();
    HookManager::loadCache();
    expect(HookManager::getInterceptorsForHook(GetString::class))->toHaveCount(1);
});

test('Can cache same interceptor twice', function () {
    HookManager::registerHook(GetString::class);
    HookManager::registerInterceptor(AppendRandomString::class);
    HookManager::registerInterceptor(AppendRandomString::class);
    HookManager::createCache();
    HookManager::loadCache();
    expect(collect(HookManager::getInterceptorsForHook(GetString::class))->flatten(1))->toHaveCount(2);
});

test('Cache are restored on boot if exists', function () {
    HookManager::createCache();
    $this->app->forgetInstance('hooks_manager');
    HookManager::clearResolvedInstances();
    expect(HookManager::isCached())->toBeTrue();
});

test('Throw error when cache file cannot be loaded', function () {
    file_put_contents(HookManager::getCacheFilepath(), '<?php return teer;');
    HookManager::loadCache();
})->throws('Unable to load hooks cache');
