<?php

namespace UniDeal\LaravelHookable\Console\Commands;

use Illuminate\Console\Command;
use UniDeal\LaravelHookable\Contracts\HookManager;

class HooksClearCommand extends Command
{
    protected $signature = 'hooks:clear';
    protected $description = 'Clear UniDeal\LaravelHookable hooks cache';

    public function handle(HookManager $hookManager): int
    {

        $hookManager->clearCache();
        if (!$hookManager->isCached()) {
            $this->components->info('Compiled hooks cleared successfully.');
        }

        return self::SUCCESS;
    }
}
