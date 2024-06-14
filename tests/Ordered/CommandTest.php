<?php

use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Artisan;
use QuixLabs\LaravelHookSystem\Console\Commands\HooksCacheCommand;
use QuixLabs\LaravelHookSystem\Console\Commands\HooksClearCommand;
use QuixLabs\LaravelHookSystem\Console\Commands\HooksStatusCommand;
use QuixLabs\LaravelHookSystem\Facades\HookManager;
use QuixLabs\LaravelHookSystem\HookRegistry;
use Workbench\App\Hooks\GetArray;
use Workbench\App\Hooks\GetString;
use Workbench\App\Interceptors\AppendPriority2;
use Workbench\App\Interceptors\AppendPriority4;

test('Can call hooks:cache', function () {
    $this->artisan(HooksCacheCommand::class)->assertSuccessful();
    HookManager::reloadCache();
    expect(HookManager::isCached())->toBeTrue();
});

test('Can call hooks:clear', function () {
    HookManager::createCache();
    HookManager::reloadCache();
    expect(HookManager::isCached())->toBeTrue();
    $this->artisan(HooksClearCommand::class)->assertSuccessful();
    HookManager::reloadCache();
    expect(HookManager::isCached())->toBeFalse();
});

test('Can call hooks:status', function () {
    $this->artisan(HooksStatusCommand::class)->assertSuccessful();
});

test('hooks:status show warning when cached', function () {
    HookManager::createCache();
    HookManager::reloadCache();
    expect(HookManager::isCached())->toBeTrue();
    $this->artisan(HooksStatusCommand::class)->assertSuccessful()->expectsOutputToContain('Hooks are actually cached!');
});

test("hooks:status show doesn't warning when not cached", function () {
    $this->artisan(HooksStatusCommand::class)->assertSuccessful()->doesntExpectOutputToContain('Hooks are actually cached!');
});

test('hooks:status show all hooks', function () {
    HookRegistry::registerHook(GetString::class);
    HookRegistry::registerHook(GetArray::class);

    // Keep below multiple call, not work due to termwind multiline if expects are chained
    $this->artisan(HooksStatusCommand::class)->assertSuccessful()->expectsOutputToContain(GetArray::class);
    $this->artisan(HooksStatusCommand::class)->assertSuccessful()->expectsOutputToContain(GetString::class);
});

test('hooks:status show all interceptors', function () {
    HookRegistry::registerHook(GetString::class);
    HookRegistry::registerInterceptor(AppendPriority2::class);
    HookRegistry::registerInterceptor(AppendPriority4::class);

    // Keep below multiple call, not work due to termwind multiline if expects are chained
    $this->artisan(HooksStatusCommand::class)->assertSuccessful()->expectsOutputToContain(AppendPriority2::class);
    $this->artisan(HooksStatusCommand::class)->assertSuccessful()->expectsOutputToContain(AppendPriority4::class);
});

test('hooks:status display message when no hooks are registered', function () {
    $this->app->forgetInstance('hooks_manager');
    HookManager::clearResolvedInstances();
    $this->artisan(HooksStatusCommand::class)
        ->assertSuccessful()->expectsOutputToContain('No hooks have been registered');
});

test('Display hooks cached information when not cached in about config', function () {
    Artisan::call(AboutCommand::class);
    $output = Artisan::output();
    expect(preg_match("/Hooks\s[.]+\sNOT CACHED/", $output))->toBe(1);
});

test('Display hooks cached information when cached in about config', function () {
    HookManager::createCache();
    HookManager::reloadCache();
    Artisan::call(AboutCommand::class);
    $output = Artisan::output();
    expect(preg_match("/Hooks\s[.]+\sCACHED/", $output))->toBe(1);
});
