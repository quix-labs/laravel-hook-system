<?php

namespace Workbench\App\Interceptors;

use QuixLabs\LaravelHookSystem\Enums\ActionWhenMissing;
use QuixLabs\LaravelHookSystem\Utils\Intercept;
use Workbench\App\Hooks\GetString;

class InvalidCallable
{
    #[Intercept(GetString::class, ActionWhenMissing::REGISTER_HOOK, 0)]
    private function appendPriority2(GetString $hook): void
    {
        $hook->string .= '-invalid';
    }
}
