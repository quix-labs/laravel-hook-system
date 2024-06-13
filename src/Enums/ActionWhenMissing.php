<?php

namespace QuixLabs\LaravelHookSystem\Enums;

enum ActionWhenMissing
{
    case THROW_ERROR;
    case SKIP;
    case REGISTER_HOOK;
}
