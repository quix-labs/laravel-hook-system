<?php

namespace Workbench\App\Hooks;

use QuixLabs\LaravelHookSystem\Hook;

class GetArray extends Hook
{
    public function __construct(public array &$array) {}
}
