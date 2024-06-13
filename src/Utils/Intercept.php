<?php

namespace QuixLabs\LaravelHookSystem\Utils;

use QuixLabs\LaravelHookSystem\Enums\ActionWhenMissing;
use QuixLabs\LaravelHookSystem\Hook;

#[\Attribute(\Attribute::TARGET_METHOD | \Attribute::IS_REPEATABLE | \Attribute::TARGET_CLASS_CONSTANT)]
class Intercept
{
    /**
     * @param class-string<Hook> $hook
     */
    public function __construct(
        public string            $hook,
        public ActionWhenMissing $actionWhenMissing = ActionWhenMissing::THROW_ERROR,
        public int               $priority = 0
    )
    {
    }
}
