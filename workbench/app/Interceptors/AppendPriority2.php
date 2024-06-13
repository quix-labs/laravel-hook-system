<?php

namespace Workbench\App\Interceptors;

use Illuminate\Support\Str;
use QuixLabs\LaravelHookSystem\Enums\ActionWhenMissing;
use QuixLabs\LaravelHookSystem\Utils\Intercept;
use Workbench\App\Hooks\GetString;

class AppendPriority2
{
    #[Intercept(GetString::class, ActionWhenMissing::SKIP, 2)]
    public static function appendPriority2(GetString $hook): void
    {
        $hook->string .= "-priority-2";
    }
}
