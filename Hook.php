<?php

namespace UniDeal\LaravelHookable;

abstract class Hook
{
    public static function send(&...$args): static
    {
        /** @phpstan-ignore-next-line */
        $instance = new static(...$args);

        /** @var \UniDeal\LaravelHookable\Contracts\HookManager $hookManager */
        $hookManager = app(\UniDeal\LaravelHookable\Contracts\HookManager::class);

        $interceptors = $hookManager->getInterceptorsForHook(static::class);

        /** @note Don't use collection->each because not work with pointer */
        foreach ($interceptors as $callables) {
            foreach ($callables as $callable) {
                $callable($instance);
            }
        }
        return $instance;
    }
}
