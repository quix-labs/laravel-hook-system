<?php

namespace QuixLabs\LaravelHookSystem\Hooks;

use QuixLabs\LaravelHookSystem\Facades\HookManager;
use QuixLabs\LaravelHookSystem\Hook;
use QuixLabs\LaravelHookSystem\Interfaces\FullyCacheable;

class GetHooksTable extends Hook implements FullyCacheable
{
    public function __construct(public array &$rows)
    {
    }

    public static function initialInstance(): static
    {
        $hooks = collect(HookManager::getHooks())
            ->mapWithKeys(fn (string|Hook $hook) => [$hook => HookManager::getInterceptorsForHook($hook)])->toArray();
        $rows = collect($hooks)->map(function (array $interceptors, string $hookClass) {
            $callables = collect($interceptors)
                ->map(fn (array $callables, int $priority) => implode('<br/>', array_map(
                    fn (callable $callable) => static::_callableToString($callable), $callables)
                ))->join('<br/>');

            $priorities = collect($interceptors)
                ->map(fn (array $callables, int $priority) => implode('<br/>', array_fill(0, count($callables), $priority)))
                ->join('<br/>');

            return [
                'Hook' => $hookClass,
                'Interceptors' => $callables,
                'Priority' => $priorities,
                'Fully Cacheable' => HookManager::isFullyCacheable($hookClass) ? 'YES' : 'NO',
                'Fully Cached' => HookManager::isFullyCached($hookClass) ? 'YES' : 'NO',
            ];
        })->toArray();

        /** @phpstan-ignore-next-line */
        return new static($rows);
    }

    public static function _callableToString(callable $callable): string
    {
        if (is_array($callable)) {
            $class = is_object($callable[0]) ? get_class($callable[0]) : $callable[0];
            $method = $callable[1];
        } elseif (is_string($callable) && str_contains($callable, '::')) {
            [$class, $method] = explode('::', $callable);
        } else {
            $class = get_class($callable);
            $method = '__invoke';
        }

        $reflection = new \ReflectionMethod($class, $method);
        $line = $reflection->getStartLine();

        return sprintf('%s@%s:%d', $class, $method, $line);

    }
}
