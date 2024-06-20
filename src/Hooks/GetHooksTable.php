<?php

namespace QuixLabs\LaravelHookSystem\Hooks;

use QuixLabs\LaravelHookSystem\Hook;

class GetHooksTable extends Hook
{
    public function __construct(public array &$rows) {}
}
