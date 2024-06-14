<?php

namespace QuixLabs\LaravelHookSystem;

use Illuminate\Support\Facades\File;
use QuixLabs\LaravelHookSystem\Enums\ActionWhenMissing;
use QuixLabs\LaravelHookSystem\Utils\Intercept;

class HookManager
{
    /**
     * @var array<class-string<Hook>,array<int,array<callable>>>
     */
    protected array $hooks = [];

    /**
     * @var true
     */
    private bool $cached = false;

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

                if (!array_key_exists($attributeInstance->hook, $this->hooks)) {
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
                if (!is_callable($callable)) {
                    continue;
                }

                $this->hooks[$hook][$priority][] = [$class, $method->getName()];
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
        if (!array_key_exists($hookClass, $this->hooks)) {
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
        if (!File::exists($this->getCacheFilepath())) {
            $this->cached = false;
            return;
        }

        try {
            $this->hooks = require $this->getCacheFilepath();
            $this->cached = true;
        } catch (\Throwable $e) {
            $this->clearCache();
            $this->cached = false;
        }
    }

    public function createCache(): void
    {
        $hooks = array_filter($this->hooks);
        File::put($this->getCacheFilepath(), "<?php\nreturn " . var_export($hooks, true) . ';');
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
}
