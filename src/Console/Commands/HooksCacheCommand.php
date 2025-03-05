<?php

namespace QuixLabs\LaravelHookSystem\Console\Commands;

use Illuminate\Console\Command;
use QuixLabs\LaravelHookSystem\Facades\HookManager;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand("hooks:cache")]
class HooksCacheCommand extends Command
{
    protected $signature = 'hooks:cache';

    protected $description = 'Cache QuixLabs\LaravelHookSystem hooks';

    public function handle(): int
    {
        HookManager::createCache();
        $this->components->info('Hooks cached successfully.');

        return self::SUCCESS;
    }
}
