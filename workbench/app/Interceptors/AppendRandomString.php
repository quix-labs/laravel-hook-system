<?php

namespace Workbench\App\Interceptors;

use Illuminate\Support\Str;
use QuixLabs\LaravelHookSystem\Enums\ActionWhenMissing;
use QuixLabs\LaravelHookSystem\Utils\Intercept;
use Workbench\App\Hooks\GetString;

class AppendRandomString
{
    #[Intercept(GetString::class, ActionWhenMissing::SKIP, 0)]
    public static function appendRandomString(GetString $hook): void
    {
        $hook->string .= Str::random(16);
    }
}
