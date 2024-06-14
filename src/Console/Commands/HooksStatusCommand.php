<?php

namespace QuixLabs\LaravelHookSystem\Console\Commands;

use Illuminate\Console\Command;
use QuixLabs\LaravelHookSystem\Facades\HookManager;
use QuixLabs\LaravelHookSystem\Hooks\GetHooksTable;
use QuixLabs\LaravelHookSystem\Utils\CommandTable;

class HooksStatusCommand extends Command
{
    protected $signature = 'hooks:status';

    protected $description = 'List QuixLabs\LaravelHookSystem hooks';

    public function handle(): int
    {
        if (HookManager::isCached()) {
            $this->components->warn('Hooks are actually cached!');
        }

        $rows = [];
        GetHooksTable::send($rows);
        if (count($rows) > 0) {
            $this->output->writeln(CommandTable::asString($rows));
        } else {
            $this->line('No hooks have been registered.');
        }

        return self::SUCCESS;
    }
}
