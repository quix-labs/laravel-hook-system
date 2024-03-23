<?php

namespace UniDeal\LaravelHookable\Console\Commands;

use Illuminate\Console\Command;
use UniDeal\LaravelHookable\Facades\HookManager;

class HooksClearCommand extends Command
{
    protected $signature = 'hooks:clear';
    protected $description = 'Clear UniDeal\LaravelHookable hooks cache';

    public function handle(): int
    {
        HookManager::clearCache();
        if (!HookManager::isCached()) {
            $this->components->info('Compiled hooks cleared successfully.');
        }

        return self::SUCCESS;
    }
}
