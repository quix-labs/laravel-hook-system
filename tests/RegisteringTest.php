<?php

use QuixLabs\LaravelHookSystem\Facades\HookManager;
use Workbench\App\Hooks\GetString;
use Workbench\App\Interceptors\RegisterNonExistingHook;
use Workbench\App\Interceptors\SkipNonExistingHook;
use Workbench\App\Interceptors\ThrowNonExistingHook;

it('Can register hook', function () {
    HookManager::registerHook(GetString::class);
})->throwsNoExceptions();

// Interceptor ActionWhenMissing testing
test('Interceptor skipped when missing and ActionWhenMissing::SKIP', function () {
    HookManager::registerInterceptor(SkipNonExistingHook::class);
    expect(HookManager::getHooks())->toBeArray()
        ->not->toContain("Workbench\App\Interceptors\InvalidHook")
        ->and(HookManager::getInterceptorsForHook("Workbench\App\Interceptors\InvalidHook"))->toBeEmpty();
});

test('Interceptor throw error when missing hook and ActionWhenMissing::THROW_ERROR', function () {
    HookManager::registerInterceptor(ThrowNonExistingHook::class);
})->throws("Hook Workbench\App\Interceptors\InvalidHook is not registered!");

test('Interceptor register new hook when missing and ActionWhenMissing::REGISTER', function () {
    HookManager::registerInterceptor(RegisterNonExistingHook::class);
    expect(HookManager::getHooks())->toBeArray()->toContain("Workbench\App\Interceptors\InvalidHook");
});
