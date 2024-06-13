<?php

namespace QuixLabs\LaravelHookSystem\Console\Commands;

use Illuminate\Console\Command;
use QuixLabs\LaravelHookSystem\Facades\HookManager;

class HooksClearCommand extends Command
{
    protected $signature = 'hooks:clear';
    protected $description = 'Clear QuixLabs\LaravelHookSystem hooks cache';

    public function handle(): int
    {
        HookManager::clearCache();
        if (!HookManager::isCached()) {
            $this->components->info('Compiled hooks cleared successfully.');
        }

        return self::SUCCESS;
    }
}
