<?php

namespace UniDeal\LaravelHookable\Contracts;

use UniDeal\LaravelHookable\Hook;

interface HookManager
{
    public function registerHook(Hook|string $hook): void;

    public function registerInterceptor(string $class): void;


    /**
     * @return array<Hook|string>
     */
    public function getHooks(): array;

    /**
     * @param Hook|string $hook
     * @return array<int,callable[]>
     */
    public function getInterceptorsForHook(Hook|string $hook): array;

    public function getCacheFilepath(): string;
    public function loadCache(): void;
    public function createCache(): void;
    public function clearCache(): void;
    public function isCached(): bool;
}
