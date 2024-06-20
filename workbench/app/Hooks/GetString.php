<?php

namespace Workbench\App\Hooks;

use QuixLabs\LaravelHookSystem\Hook;

class GetString extends Hook
{
    public function __construct(public string &$string) {}
}
