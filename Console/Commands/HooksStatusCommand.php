<?php

namespace UniDeal\LaravelHookable\Console\Commands;

use Illuminate\Console\Command;
use UniDeal\LaravelHookable\Contracts\HookManager;
use UniDeal\LaravelHookable\Hook;
use UniDeal\LaravelHookable\Hooks\GetHooksTable;
use UniDeal\LaravelHookable\Utils\CommandTable;

class HooksStatusCommand extends Command
{
    protected $signature = 'hooks:status';
    protected $description = 'List UniDeal\LaravelHookable hooks';

    public function handle(HookManager $hookManager): int
    {
        if ($hookManager->isCached()) {
            $this->components->warn("Hooks are actually cached!");
        }

        $hooks = collect($hookManager->getHooks())
            ->mapWithKeys(fn(string|Hook $hook) => [$hook => $hookManager->getInterceptorsForHook($hook)]);
        if (count($hooks) > 0) {
            $this->showHooks($hooks->toArray());
        } else {
            $this->line('No hooks have been registered.');
        }

        return self::SUCCESS;
    }

    /**
     * @param array<string,array<int,array<callable>>> $hooks
     * @return void
     */
    private function showHooks(array $hooks): void
    {
        $rows = collect($hooks)->map(function (array $interceptors, string $hookClass) {
            $callables = collect($interceptors)
                ->map(fn(array $callables, int $priority) => join("<br/>", array_map(
                        fn(callable $callable) => $this->_callableToString($callable), $callables)
                ))->join("<br/>");

            $priorities = collect($interceptors)
                ->map(fn(array $callables, int $priority) => join("<br/>", array_fill(0, count($callables), $priority)))
                ->join("<br/>");

            return [
                'Hook'         => $hookClass,
                'Interceptors' => $callables,
                'Priority'     => $priorities,
            ];
        })->toArray();

        CommandTable::display($rows, GetHooksTable::class);
    }

    private function _callableToString(callable $callable): string
    {
        if (is_array($callable)) {
            $class = is_object($callable[0]) ? get_class($callable[0]) : $callable[0];
            $method = $callable[1];
        } elseif (is_string($callable) && str_contains($callable, '::')) {
            list($class, $method) = explode('::', $callable);
        } else {
            $class = get_class($callable);
            $method = '__invoke';
        }

        $reflection = new \ReflectionMethod($class, $method);
        $line = $reflection->getStartLine();

        return sprintf('%s@%s:%d', $class, $method, $line);

    }
}
