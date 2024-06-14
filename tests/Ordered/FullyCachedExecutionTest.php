<?php

use QuixLabs\LaravelHookSystem\Facades\HookManager;
use QuixLabs\LaravelHookSystem\HookRegistry;
use Workbench\App\Hooks\GetStringFullyCacheable;
use Workbench\App\Interceptors\AppendRandomStringFullyCacheable;
use Workbench\App\Interceptors\AppendRandomStringFullyCacheablePrevent;

test('Can execute when cache are not generated', function () {
    HookRegistry::registerHook(GetStringFullyCacheable::class);
    HookRegistry::registerInterceptor(AppendRandomStringFullyCacheable::class);
    HookRegistry::registerInterceptor(AppendRandomStringFullyCacheable::class);
    $string = '';
    GetStringFullyCacheable::send($string);
    expect($string)->toHaveLength(32);
});


test('Cached hook reuse cache each time', function () {
    HookRegistry::registerHook(GetStringFullyCacheable::class);
    HookRegistry::registerInterceptor(AppendRandomStringFullyCacheable::class);
    HookManager::createCache();
    HookManager::reloadCache();

    $string1 = "";
    GetStringFullyCacheable::send($string1);
    $string2 = "";
    GetStringFullyCacheable::send($string2);
    expect($string1)->not->toBeEmpty()
        ->and($string1)->toBe($string2);
});

test('Fully cache restored on boot', function () {
    HookRegistry::registerHook(GetStringFullyCacheable::class);
    HookRegistry::registerInterceptor(AppendRandomStringFullyCacheable::class);
    HookManager::createCache();
    HookManager::reloadCache();

    $this->app->forgetInstance("hooks_manager");
    HookManager::clearResolvedInstances();
    expect(HookManager::isCached())->toBeTrue()
        ->and(HookManager::getCachedHook(GetStringFullyCacheable::class))->not->toBeEmpty();
});

test("Cache doesn't destroy initial State", function () {
    HookRegistry::registerHook(GetStringFullyCacheable::class);
    HookManager::createCache();
    HookManager::reloadCache();

    $this->app->forgetInstance("hooks_manager");
    HookManager::clearResolvedInstances();
    expect(HookManager::isCached())->toBeTrue()
        ->and(HookManager::getCachedHook(GetStringFullyCacheable::class))->not->toBeEmpty();
});

test("Interceptor can prevent full cache", function () {
    HookRegistry::registerHook(GetStringFullyCacheable::class);
    HookRegistry::registerInterceptor(AppendRandomStringFullyCacheablePrevent::class);
    HookManager::createCache();
    HookManager::reloadCache();

    expect(HookManager::isCached())->toBeTrue()
        ->and(HookManager::isFullyCached(GetStringFullyCacheable::class))->toBeFalse()
        ->and(HookManager::getCachedHook(GetStringFullyCacheable::class))->toBeNull();
});
