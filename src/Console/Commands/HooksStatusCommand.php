<?php

namespace QuixLabs\LaravelHookSystem\Console\Commands;

use Illuminate\Console\Command;
use QuixLabs\LaravelHookSystem\Facades\HookManager;
use QuixLabs\LaravelHookSystem\Hook;
use QuixLabs\LaravelHookSystem\Hooks\GetHooksTable;
use QuixLabs\LaravelHookSystem\Utils\CommandTable;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand("hooks:status")]
class HooksStatusCommand extends Command
{
    protected $signature = 'hooks:status';

    protected $description = 'List QuixLabs\LaravelHookSystem hooks';

    public function handle(): int
    {
        if (HookManager::isCached()) {
            $this->components->warn('Hooks are actually cached!');
        }

        $hooks = collect(HookManager::getHooks())
            ->mapWithKeys(fn(string|Hook $hook) => [$hook => HookManager::getInterceptorsForHook($hook)])->toArray();
        $rows = collect($hooks)->map(function (array $interceptors, string $hookClass) {
            $callables = collect($interceptors)
                ->map(fn(array $callables, int $priority) => implode('<br/>', array_map(
                        fn(callable $callable) => static::_callableToString($callable), $callables)
                ))->join('<br/>');

            $priorities = collect($interceptors)
                ->map(fn(array $callables, int $priority) => implode('<br/>', array_fill(0, count($callables), $priority)))
                ->join('<br/>');

            return [
                'Hook'            => $hookClass,
                'Interceptors'    => $callables,
                'Priority'        => $priorities,
                'Fully Cacheable' => HookManager::isFullyCacheable($hookClass) ? 'YES' : 'NO',
                'Fully Cached'    => HookManager::isFullyCached($hookClass) ? 'YES' : 'NO',
            ];
        })->toArray();

        GetHooksTable::send($rows);
        if (count($rows) > 0) {
            $this->output->writeln(CommandTable::asString($rows));
        } else {
            $this->line('No hooks have been registered.');
        }

        return self::SUCCESS;
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
