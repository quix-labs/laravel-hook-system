<?php

namespace UniDeal\LaravelHookable\Console\Commands;

use Illuminate\Console\Command;
use UniDeal\LaravelHookable\Facades\HookManager;

class HooksCacheCommand extends Command
{
    protected $signature = 'hooks:cache';
    protected $description = 'Cache UniDeal\LaravelHookable hooks';

    public function handle(): int
    {

        HookManager::createCache();
        if (HookManager::isCached()) {
            $this->components->info('Hooks cached successfully.');
        }

        return self::SUCCESS;
    }
}
