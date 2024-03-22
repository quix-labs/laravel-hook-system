<?php

namespace UniDeal\LaravelHookable\Hooks;

use UniDeal\LaravelHookable\Hook;

class GetHooksTable extends Hook
{
    public function __construct(public array &$rows)
    {
    }
}
