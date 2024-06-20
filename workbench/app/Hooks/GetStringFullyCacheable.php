<?php

namespace Workbench\App\Hooks;

use QuixLabs\LaravelHookSystem\Hook;
use QuixLabs\LaravelHookSystem\Interfaces\FullyCacheable;

class GetStringFullyCacheable extends Hook implements FullyCacheable
{
    public function __construct(public string &$string) {}

    public static function initialInstance(): static
    {
        $string = '';

        return new static($string);
    }
}
