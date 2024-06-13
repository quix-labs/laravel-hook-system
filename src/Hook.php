<?php

namespace QuixLabs\LaravelHookSystem;

use QuixLabs\LaravelHookSystem\Facades\HookManager;

abstract class Hook
{
    public static function send(&...$args): static
    {
        /** @phpstan-ignore-next-line */
        $instance = new static(...$args);

        $interceptors = HookManager::getInterceptorsForHook(static::class);

        /** @note Don't use collection->each because not work with pointer */
        foreach ($interceptors as $callables) {
            foreach ($callables as $callable) {
                $callable($instance);
            }
        }

        return $instance;
    }
}
