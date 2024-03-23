<?php

namespace UniDeal\LaravelHookable;

use Illuminate\Support\Facades\File;
use UniDeal\LaravelHookable\Enums\ActionWhenMissing;
use UniDeal\LaravelHookable\Utils\Intercept;

class HookManager
{
    /**
     * @var array<class-string<Hook>,array<int,array<callable>>>
     */
    protected array $hooks = [];

    public function __construct()
    {
        if ($this->isCached()) {
            $this->loadCache();
        }
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
                        case ActionWhenMissing::REGISTER_HOOK;
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
     * @param Hook|string $hook
     * @return array<int,callable[]>
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

    public function loadCache(): void
    {
        try {
            $this->hooks = require $this->getCacheFilepath();
        } catch (\Throwable $e) {
            throw new \RuntimeException("Unable to load hooks cache: " . $e->getMessage());
        }
    }

    public function createCache(): void
    {
        $hooks = array_filter($this->hooks);
        File::put($this->getCacheFilepath(), "<?php\nreturn " . var_export($hooks, true) . ";");
    }

    public function clearCache(): void
    {
        if (File::exists($this->getCacheFilepath())) {
            File::delete($this->getCacheFilepath());
        }
    }


    public function isCached(): bool
    {
        return File::exists($this->getCacheFilepath());
    }
}
