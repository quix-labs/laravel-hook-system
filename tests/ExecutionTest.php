<?php

use QuixLabs\LaravelHookSystem\Facades\HookManager;
use Workbench\App\Hooks\GetString;
use Workbench\App\Interceptors\AppendPriority2;
use Workbench\App\Interceptors\AppendPriority4;
use Workbench\App\Interceptors\AppendPriority5;
use Workbench\App\Interceptors\AppendRandomString;

test('Single interceptor correctly executed', function () {
    HookManager::registerHook(GetString::class);
    HookManager::registerInterceptor(AppendRandomString::class);
    $string = '';
    GetString::send($string);
    expect($string)->toHaveLength(16);
});

test('Same interceptor used twice correctly executed twice', function () {
    HookManager::registerHook(GetString::class);
    HookManager::registerInterceptor(AppendRandomString::class);
    HookManager::registerInterceptor(AppendRandomString::class);
    $string = '';
    GetString::send($string);
    expect($string)->toHaveLength(32);
});

test('Interceptors priority are respected', function () {
    HookManager::registerHook(GetString::class);
    HookManager::registerInterceptor(AppendPriority5::class);
    HookManager::registerInterceptor(AppendPriority2::class);
    HookManager::registerInterceptor(AppendPriority4::class);
    $string = '';
    GetString::send($string);
    expect($string)->toBe('-priority-2-priority-4-priority-5');
});
