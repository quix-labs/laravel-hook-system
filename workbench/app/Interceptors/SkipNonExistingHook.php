<?php

namespace Workbench\App\Interceptors;

use QuixLabs\LaravelHookSystem\Enums\ActionWhenMissing;
use QuixLabs\LaravelHookSystem\Utils\Intercept;

class SkipNonExistingHook
{
    #[Intercept(InvalidHook::class, ActionWhenMissing::SKIP, 0)]
    public static function handleNonExistingHook(InvalidHook $hook) {}
}
