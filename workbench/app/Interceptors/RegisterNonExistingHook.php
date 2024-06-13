<?php

namespace Workbench\App\Interceptors;

use QuixLabs\LaravelHookSystem\Enums\ActionWhenMissing;
use QuixLabs\LaravelHookSystem\Utils\Intercept;

class RegisterNonExistingHook
{
    #[Intercept(InvalidHook::class, ActionWhenMissing::REGISTER_HOOK, 0)]
    public static function handleNonExistingHook(InvalidHook $hook)
    {

    }
}
