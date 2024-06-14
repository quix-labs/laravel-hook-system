<?php

namespace QuixLabs\LaravelHookSystem;

use Illuminate\Support\Facades\File;
use QuixLabs\LaravelHookSystem\Enums\ActionWhenMissing;
use QuixLabs\LaravelHookSystem\Interfaces\FullyCacheable;
use QuixLabs\LaravelHookSystem\Utils\Intercept;

class HookManager
{
    /**
     * @var array<class-string<Hook>,array<int,array<callable>>>
     */
    protected array $hooks = [];

    private bool $cached = false;

    /**
     * @var array<class-string<Hook>,Hook>
     */
    private array $fullCache = [];

    private array $preventedFullCacheHooks = [];

    /**
     * @throws \Exception
     */
    public function __construct()
    {
        $this->reloadCache();
    }

    public function registerHook(Hook|string $hook): void
    {
        $hookClass = is_string($hook) ? $hook : $hook::class;
        if (array_key_exists($hook, $this->hooks)) {
            return;
        }
        $this->hooks[$hookClass] = [];
    }

    public function registerInterceptor(string $class): void
    {
        $reflection = new \ReflectionClass($class);

        foreach ($reflection->getMethods() as $method) {
            foreach ($method->getAttributes(Intercept::class) as $attribute) {

                /** @var Intercept $attributeInstance */
                $attributeInstance = $attribute->newInstance();
                $hook = $attributeInstance->hook;
                $priority = $attributeInstance->priority;
                $preventFullCache = $attributeInstance->preventFullCache;

                if (! array_key_exists($attributeInstance->hook, $this->hooks)) {
                    switch ($attributeInstance->actionWhenMissing) {
                        case ActionWhenMissing::THROW_ERROR:
                            throw new \Exception("Hook {$hook} is not registered!");
                        case ActionWhenMissing::SKIP:
                            continue 2;
                        case ActionWhenMissing::REGISTER_HOOK:
                            $this->registerHook($hook);
                    }
                }

                $callable = [$class, $method->getName()];
                if (! is_callable($callable)) {
                    continue;
                }

                $this->hooks[$hook][$priority][] = [$class, $method->getName()];
                if ($preventFullCache) {
                    $this->preventedFullCacheHooks = array_unique(array_merge($this->preventedFullCacheHooks, [$hook]));
                }
            }
        }
    }

    /**
     * @return array<Hook|string>
     */
    public function getHooks(): array
    {
        return array_keys($this->hooks);
    }

    /**
     * @return array<int,callable[]>
     *
     * @throws \Exception
     */
    public function getInterceptorsForHook(Hook|string $hook): array
    {
        $hookClass = is_string($hook) ? $hook : $hook::class;
        if (! array_key_exists($hookClass, $this->hooks)) {
            return [];
        }
        $hooks = $this->hooks[$hookClass];
        ksort($hooks);

        return $hooks;
    }

    public function getCacheFilepath(): string
    {
        return base_path('bootstrap/cache/hooks.php');
    }

    public function reloadCache(): void
    {

        if (! File::exists($this->getCacheFilepath())) {
            $this->cached = false;

            return;
        }
        try {
            $cache = require $this->getCacheFilepath();
            $this->hooks = $cache['hooks'];
            $this->fullCache = $cache['fullCache'];
            $this->cached = true;
        } catch (\Throwable $e) {
            report($e);
            $this->clearCache();
            $this->cached = false;
        }
    }

    public function createCache(): void
    {
        /** @var class-string<Hook> $hook */
        foreach ($this->hooks as $hook => $callbacks) {
            if ($this->isFullyCacheable($hook)) {
                /** @phpstan-ignore-next-line */
                $this->fullCache[$hook] = $hook::initialInstance()->sendThroughInterceptors();
            }
        }
        $cache = ['hooks' => array_filter($this->hooks), 'fullCache' => $this->fullCache];
        File::put($this->getCacheFilepath(), "<?php\nreturn ".var_export($cache, true).';');
    }

    public function clearCache(): void
    {
        if (File::exists($this->getCacheFilepath())) {
            File::delete($this->getCacheFilepath());
        }
        $this->cached = false;
    }

    public function isCached(): bool
    {
        return $this->cached;
    }

    // Full cache management

    /**
     * @param  class-string<Hook>  $hook
     */
    public function isFullyCached(string $hook): bool
    {
        return array_key_exists($hook, $this->fullCache);
    }

    /**
     * @param  class-string<Hook>  $hook
     */
    public function isFullyCacheable(string $hook): bool
    {
        return is_subclass_of($hook, FullyCacheable::class)
            && ! $this->isPreventedFromFullCache($hook);
    }

    /**
     * @param  class-string<Hook>  $hook
     */
    public function getCachedHook(string $hook): ?Hook
    {
        return $this->fullCache[$hook] ?? null;
    }

    private function isPreventedFromFullCache(string $hook): bool
    {
        return in_array($hook, $this->preventedFullCacheHooks, true);
    }
}
