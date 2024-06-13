<?php

namespace Workbench\App\Interceptors;

use Illuminate\Support\Str;
use QuixLabs\LaravelHookSystem\Enums\ActionWhenMissing;
use QuixLabs\LaravelHookSystem\Utils\Intercept;
use Workbench\App\Hooks\GetString;

class AppendPriority4
{
    #[Intercept(GetString::class, ActionWhenMissing::SKIP, 4)]
    public static function appendPriority4(GetString $hook): void
    {
        $hook->string .= "-priority-4";
    }
}
