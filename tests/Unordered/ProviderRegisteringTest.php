<?php

use QuixLabs\LaravelHookSystem\Facades\HookManager;
use Workbench\App\Hooks\GetString;
use Workbench\App\Interceptors\AppendRandomString;

test('Ensure hook and interceptors can be registered when internal Provider loaded after externals', function () {
    expect(HookManager::getHooks())->toContain(GetString::class)
        ->and(collect(HookManager::getInterceptorsForHook(GetString::class))->flatten(1))
        ->toContain([AppendRandomString::class, 'appendRandomString']);
});
