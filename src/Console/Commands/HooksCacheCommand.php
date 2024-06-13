<?php

namespace QuixLabs\LaravelHookSystem\Console\Commands;

use Illuminate\Console\Command;
use QuixLabs\LaravelHookSystem\Facades\HookManager;

class HooksCacheCommand extends Command
{
    protected $signature = 'hooks:cache';

    protected $description = 'Cache QuixLabs\LaravelHookSystem hooks';

    public function handle(): int
    {
        HookManager::createCache();
        if (HookManager::isCached()) {
            $this->components->info('Hooks cached successfully.');
        }

        return self::SUCCESS;
    }
}
