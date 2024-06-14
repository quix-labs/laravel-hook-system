<?php

use QuixLabs\LaravelHookSystem\HookRegistry;
use Workbench\App\Hooks\GetString;
use Workbench\App\Interceptors\AppendPriority2;
use Workbench\App\Interceptors\AppendPriority4;
use Workbench\App\Interceptors\AppendPriority5;
use Workbench\App\Interceptors\AppendRandomString;

test('Single interceptor correctly executed', function () {
    HookRegistry::registerHook(GetString::class);
    HookRegistry::registerInterceptor(AppendRandomString::class);
    $string = '';
    GetString::send($string);
    expect($string)->toHaveLength(16);
});

test('Same interceptor used twice correctly executed twice', function () {
    HookRegistry::registerHook(GetString::class);
    HookRegistry::registerInterceptor(AppendRandomString::class);
    HookRegistry::registerInterceptor(AppendRandomString::class);
    $string = '';
    GetString::send($string);
    expect($string)->toHaveLength(32);
});

test('Interceptors priority are respected', function () {
    HookRegistry::registerHook(GetString::class);
    HookRegistry::registerInterceptor(AppendPriority5::class);
    HookRegistry::registerInterceptor(AppendPriority2::class);
    HookRegistry::registerInterceptor(AppendPriority4::class);
    $string = '';
    GetString::send($string);
    expect($string)->toBe('-priority-2-priority-4-priority-5');
});
