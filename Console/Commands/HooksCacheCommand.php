<?php

namespace UniDeal\LaravelHookable\Console\Commands;

use Illuminate\Console\Command;
use UniDeal\LaravelHookable\Contracts\HookManager;

class HooksCacheCommand extends Command
{
    protected $signature = 'hooks:cache';
    protected $description = 'Cache UniDeal\LaravelHookable hooks';

    public function handle(HookManager $hookManager): int
    {

        $hookManager->createCache();
        if ($hookManager->isCached()) {
            $this->components->info('Hooks cached successfully.');
        }

        return self::SUCCESS;
    }
}
