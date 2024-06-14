<?php

namespace QuixLabs\LaravelHookSystem;

use QuixLabs\LaravelHookSystem\Facades\HookManager;

abstract class Hook
{
    public function sendThroughInterceptors(): static
    {
        $interceptors = HookManager::getInterceptorsForHook(static::class);

        /** @note Don't use collection->each because not work with pointer */
        foreach ($interceptors as $callables) {
            foreach ($callables as $callable) {
                $callable($this);
            }
        }

        return $this;
    }

    public static function send(&...$args): static
    {
        if (! HookManager::isFullyCacheable(static::class)) {
            /** @phpstan-ignore-next-line */
            return (new static(...$args))->sendThroughInterceptors();
        }

        if (! HookManager::isFullyCached(static::class)) {
            $instance = static::initialInstance()->sendThroughInterceptors();
        } else {
            $instance = HookManager::getCachedHook(static::class);
        }

        // Clone properties to keep pointer working
        $cachedProperties = get_object_vars($instance);
        foreach ($cachedProperties as $key => $value) {

            // Try using named arguments
            if (array_key_exists($key, $args)) {
                $args[$key] = $value;

                continue;
            }

            // Else try using index
            $index = array_search($key, array_keys($cachedProperties));
            if (array_key_exists($index, $args)) {
                $args[$index] = $value;
            }
        }

        return $instance;
    }

    // Keep for fully cache
    public static function __set_state(array $state): static
    {
        return new static(...$state);
    }
}
