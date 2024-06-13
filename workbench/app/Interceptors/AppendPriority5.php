<?php

namespace Workbench\App\Interceptors;

use QuixLabs\LaravelHookSystem\Enums\ActionWhenMissing;
use QuixLabs\LaravelHookSystem\Utils\Intercept;
use Workbench\App\Hooks\GetString;

class AppendPriority5
{
    #[Intercept(GetString::class, ActionWhenMissing::SKIP, 5)]
    public static function appendPriority5(GetString $hook): void
    {
        $hook->string .= '-priority-5';
    }
}
