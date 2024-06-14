<?php

namespace Workbench\App\Interceptors;

use Illuminate\Support\Str;
use QuixLabs\LaravelHookSystem\Enums\ActionWhenMissing;
use QuixLabs\LaravelHookSystem\Utils\Intercept;
use Workbench\App\Hooks\GetStringFullyCacheable;

class AppendRandomStringFullyCacheable
{
    #[Intercept(GetStringFullyCacheable::class, ActionWhenMissing::SKIP, 0)]
    public static function appendRandomStringFullyCacheable(GetStringFullyCacheable $hook): void
    {
        $hook->string .= Str::random(16);
    }
}
