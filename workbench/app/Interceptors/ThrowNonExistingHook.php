<?php

namespace Workbench\App\Interceptors;

use QuixLabs\LaravelHookSystem\Enums\ActionWhenMissing;
use QuixLabs\LaravelHookSystem\Utils\Intercept;

class ThrowNonExistingHook
{
    #[Intercept(InvalidHook::class, ActionWhenMissing::THROW_ERROR, 0)]
    public static function handleNonExistingHook(InvalidHook $hook)
    {

    }
}
